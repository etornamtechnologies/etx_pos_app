<?php

namespace App\Http\Controllers;
use App\Batch;
use App\Product;
use App\Purchase;
use Carbon\Carbon;
use App\OutPayment;
use App\PurchaseEntry;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\InventoryController;
use Symfony\Component\HttpFoundation\Request;



class PurchaseController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('eroles:admin,manager', ['except'=> []]);
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $request->query('filter') : "";
        try{
            $purchases = [];
            $credits = [];
            if($request->query('type') == 'creditors') {
                $query = Purchase::where('reference_number', 'LIKE', '%'.$filter.'%')
                                ->with('out_payments')->latest()->get();
                foreach($query as $qq) {
                    $sum = 0;
                    $payments = $qq->out_payments;
                    foreach($payments as $payment) {
                        $sum = $sum + $payment->count;
                    }
                    if($sum < $qq->invoice_total_cost) {
                        array_push($credits, Purchase::where('id', $qq->id)
                                    ->with(['out_payments', 'user', 'supplier'])->first());
                    }
                }
                $result['credits'] = $credits;
                $result['code'] = 0;
            } else{
                $q = Purchase::where('reference_number', 'LIKE', '%'.$filter.'%')
                ->with(['user', 'supplier', 'out_payments'])->orderBy('created_at', 'DES'); 
                if($request->has('paginate')) {
                    $purchases = $q->paginate(10);
                } else {
                    $purchases = $q->get();
                }     
                $result['code'] = 0;
                $result['purchases'] = $purchases;          
            }
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }


    public function show(Request $request, $purchaseId)
    {
        $res = [];
        try{
            $query = DB::table('purchase_entries')->where('purchase_id', $purchaseId)
                            ->leftJoin('stock_units', 'purchase_entries.stock_unit_id', '=', 'stock_units.id')
                            ->leftJoin('products', 'purchase_entries.product_id', '=', 'products.id')
                            ->select('purchase_entries.quantity as quantity', 'products.label as product'
                                        ,'stock_units.label as stock_unit', 'purchase_entries.amount as sum'
                                        ,'purchase_entries.cost_price as cost_price');
            $entries = $query->get();
            $payments = OutPayment::where('purchase_id', $purchaseId)->with(['user'])->get();
            $res['code'] = 0;
            $res['entries'] = $entries;
            $res['payments'] = $payments;                            
        } catch(Exception $e) {
            $res['code'] = 1;
            $res['message'] = "Server error";
        }
        return response()->json($res);
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $result = [];
        $status = null;
        try{
            DB::transaction(function () use($result){
                $input = Input::all();
                $userId = Auth::user()->id;
                $supplierId = $input['summary']['supplier_id'];
                $supplierInvoiceNumber = $input['summary']['supplier_invoice'];
                $amountPaid = $input['summary']['amount_paid'] *100;
                $invoiceAmount = $input['summary']['invoice_amount'] * 100;
                $entries = $input['entries'];
                $purchaseCount = Purchase::count() + 1;
                $referenceNumber = "LEK-PUR/$purchaseCount";
                $purchase = Purchase::create([
                    'supplier_id'=> $supplierId,
                    'reference_number'=> $referenceNumber,
                    'user_id'=> $userId,
                    'invoice_number'=> $supplierInvoiceNumber,
                    'invoice_total_cost'=> $invoiceAmount,
                    'status'=> 'active'
                ]);
                //make payment
                OutPayment::create([
                    'user_id'=> Auth::user()->id,
                    'count'=> $amountPaid,
                    'purchase_id'=> $purchase->id
                ]);
                $purchaseEntries = $this->createPurchaseEntries($purchase->id, $entries);
            });
            $result['code'] = 0;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong! please contact admin";
        }
        return response()->json($result);
    }

    public function createPurchaseEntries($purchase_id, $entries) {
        foreach($entries as $entry) {
            $stockUnitId = $entry['stock_unit_id'];
            $productId = $entry['product_id'];
            $quantity = $entry['quantity'];
            $costPrice = $entry['cost_price']*100;
            $sum = $costPrice * $quantity;
            $stockData = DB::table('product_stock_unit')
                            ->where('product_id', $productId)
                            ->where('stock_unit_id', $stockUnitId)->first();
            $metricScale = $stockData->metric_scale;
            $metricQty = $quantity * $metricScale;   
            Product::where('id', $productId)->increment('stock_quantity', $metricQty);             
            PurchaseEntry::create([
                'purchase_id'=> $purchase_id,
                'product_id'=> $productId,
                'stock_unit_id'=> $stockUnitId,
                'cost_price'=> $costPrice,
                'quantity'=> $quantity,
                'amount'=> $sum,
                'metric_quantity'=> $metricQty
            ]);
            $metricCostPrice = round($costPrice/$metricScale, 0, PHP_ROUND_HALF_UP);
            InventoryController::setCostPriceFor($productId, $metricCostPrice);
            if($entry['batch_number'] && $entry['expiry_date']) {
                BatchController::addBatch($entry['batch_number'], $entry['expiry_date'], $productId, $metricQty);
            }
        }
    }
}