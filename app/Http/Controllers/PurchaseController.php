<?php

namespace App\Http\Controllers;
use App\User;
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
        $this->middleware('api_auth');
        $this->middleware('api_role:manager,admin,supervisor');
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $request->query('filter') : "";
        $refCode = $request->has('ref_code') ? $request->query('ref_code') : "";
        $supplierInv = $request->has('supplier_invoice') ? $request->query('supplier_invoice') : "";
        $supplierId = $request->has('supplier_id') ? $request->query('supplier_id') : "";
        $purchasesQuery = Purchase::when($refCode, function($query, $refCode) {
                                return $query->where('ref_code', 'LIKE', '%'.$refCode.'%');
                            })
                            ->when($supplierInv, function($query, $supplierInv) {
                                return $query->where('invoice_number', 'LIKE', '%'.$supplierInv.'%');
                            })
                            ->when($supplierId, function($query, $supplierId) {
                                return $query->where('supplier_id', $supplierId);
                            })
                            ->with(['user', 'supplier', 'out_payments'])
                            ->orderBy('created_at', 'DESC');
        if($request->has('paginate')) {
            return response()->json(['purchases'=> $purchasesQuery->paginate(10), 'code'=>0], 200);
        } else {
            return response()->json(['code'=>0, 'purchases'=> $purchasesQuery->take(100)->get()]);
        }
    }


    public function show(Request $request, $purchaseId)
    {
        $res = [];
        try{
            $purchase = Purchase::showPurchase($purchaseId);
            $payments = OutPayment::getPaymentsForPurchase($purchaseId);
            $res['code'] = 0;
            $res['entries'] = $purchase['purchase_entries'];
            $res['purchase'] = $purchase['purchase'];
            $res['payments'] = $payments;                            
        } catch(Exception $e) {
            $res['code'] = 1;
            $res['message'] = "Server error";
        }
        return response()->json($res);
    }


    public function store(Request $request)
    {
        $result = [];
        $status = null;
        try{
            DB::transaction(function () use($result, &$request){
                $input = Input::all();
                //dd($input);
                $token = $request->header('Authorization');
                $user = User::authUser($token);
                $userId = $user->id;
                $supplierId = $input['summary']['supplier_id'];
                $supplierInvoiceNumber = $input['summary']['supplier_invoice'];
                $amountPaid = $input['summary']['amount_paid'] *100;
                $invoiceAmount = $input['summary']['invoice_amount'] * 100;
                $entries = $input['entries'];
                $purchaseCount = Purchase::count() + 1;
                $config = DB::table('configs')->get()[0];
                $purchasePrefix = $config->purchase_receipt_prefix;
                $refCode = $purchasePrefix.$purchaseCount;
                $purchase = Purchase::create([
                    'supplier_id'=> $supplierId,
                    'ref_code'=> $refCode,
                    'user_id'=> $userId,
                    'invoice_number'=> $supplierInvoiceNumber,
                    'total'=> $invoiceAmount,
                    'status'=> 'active',
                    'paid'=> $amountPaid,
                ]);
                //make payment
                OutPayment::create([
                    'user_id'=> $userId,
                    'count'=> $amountPaid,
                    'purchase_id'=> $purchase->id
                ]);
                $purchaseEntries = PurchaseEntry::createPurchaseEntries($purchase->id, $entries);
            });
            $result['code'] = 0;
            $result['message'] = "Purchase invoice created successfully";
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong! please contact admin";
        }
        return response()->json($result);
    }

    // public function createPurchaseEntries($purchase_id, $entries) {
    //     foreach($entries as $entry) {
    //         $stockUnitId = $entry['stock_unit_id'];
    //         $productId = $entry['product_id'];
    //         $quantity = $entry['quantity'];
    //         $costPrice = $entry['cost_price']*100;
    //         $sum = $costPrice * $quantity;
    //         $stockData = DB::table('product_stock_unit')
    //                         ->where('product_id', $productId)
    //                         ->where('stock_unit_id', $stockUnitId)->first();
    //         $metricScale = $stockData->metric_scale;
    //         $metricQty = $quantity * $metricScale;   
    //         Product::where('id', $productId)->increment('stock_quantity', $metricQty);             
    //         PurchaseEntry::create([
    //             'purchase_id'=> $purchase_id,
    //             'product_id'=> $productId,
    //             'stock_unit_id'=> $stockUnitId,
    //             'cost_price'=> $costPrice,
    //             'quantity'=> $quantity,
    //             'amount'=> $sum,
    //             'metric_quantity'=> $metricQty
    //         ]);
    //         $metricCostPrice = round($costPrice/$metricScale, 0, PHP_ROUND_HALF_UP);
    //         InventoryController::setCostPriceFor($productId, $metricCostPrice);
    //         if($entry['batch_number'] && $entry['expiry_date']) {
    //             dd('batch exist');
    //             Batch::addProductToBatch($entry['batch_number'], $entry['expiry_date'], $productId, $metricQty);
    //         }
    //     }
    // }
}