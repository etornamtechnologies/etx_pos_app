<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:27 AM
 */

namespace App\Http\Controllers;
use App\Sale;
use App\User;
use App\Batch;
use App\Config;
use App\Product;
use App\InPayment;
use App\SaleEntry;
use App\StockUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BatchController;


class SaleController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:admin,manager,sales-rep');
        $this->middleware('api_role:admin,manager')->except(['store']);
    }

    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $request->query('filter') : "";
        $refCode = $request->has('ref_code') ? $request->query('ref_code') : "";
        $customerId = $request->has('customer_id') ? $request->query('customer_id') : "";
        $salesQuery = Sale::when($refCode, function($query, $refCode) {
                                return $query->where('ref_code', 'LIKE', '%'.$refCode.'%');
                            })
                            ->when($customerId, function($query, $customerId) {
                                return $query->where('customer_id', $customerId);
                            })
                            ->with(['user', 'customer', 'in_payments'])
                            ->orderBy('created_at', 'DESC');
        if($request->has('paginate')) {
            return response()->json(['sales'=> $salesQuery->paginate(10), 'code'=>0], 200);
        } else {
            return response()->json(['code'=>0, 'sales'=> $salesQuery->take(100)->get()]);
        }
    }

    public function store(Request $request)
    {
        $result = [];
        $saleCount = Sale::count() + 1;
        $refCode = "SALE/".$saleCount;
        $receiptData = [];
        DB::transaction(function () use($result, &$refCode, &$receiptData, &$request){
            $input = Input::all();
            $token = $request->header('Authorization');
            $user = User::AuthUser($token);
            if(!$user) {
                return response()->json(['message'=> "Not Logged In"], 401);
            }
            $entries = $input['entries'];
            $customerId = $input['summary']['customer_id'];
            $paid = $input['summary']['amount_paid'] * 100;
            $amountPaid = $paid;
            $totalCost = $this->getTotalSaleCost($entries)['total'];
            $change = $paid - $totalCost;
            if($change > 0) {
                $amountPaid = $totalCost;
            }
            $receiptEntries = $this->getTotalSaleCost($entries)['receipt_entries'];
            $receiptSummary = ['reference_number'=> $refCode, 'amount_paid'=> $paid/100
                                ,'total_cost'=> $totalCost/100, 'change'=> $change/100
                                ,'user'=> $user];
            //create sale
            $sale = Sale::create([
                'customer_id'=>$customerId,
                'user_id'=>$user->id,
                'ref_code'=>$refCode,
                'total_cost'=>$totalCost,
                'status'=>'active',
                'paid'=> $amountPaid
            ]);
            //create payment
            $this->createPayment($sale->id, $user->id, $amountPaid);
            //createEntries
            $this->createSaleEntries($sale->id, $entries); 
            $receiptData['entries'] = $receiptEntries;
            $receiptData['summary'] = $receiptSummary;
            $receiptData['shop_info'] = Config::findOrFail(1);
        });
        $result['code'] = 0;
        $result['receipt_data'] = $receiptData;
        $result['message'] = "Sale created successfully";
        return response()->json($result);
    }

    public function show(Request $request, $saleId)
    {
        $res = [];
        try{
            $query = DB::table('sale_entries')->where('sale_id', $saleId)
                            ->leftJoin('stock_units', 'sale_entries.stock_unit_id', '=', 'stock_units.id')
                            ->leftJoin('products', 'sale_entries.product_id', '=', 'products.id')
                            ->select('sale_entries.quantity as quantity', 'products.label as product'
                                        ,'stock_units.label as stock_unit', 'sale_entries.amount as sum'
                                        ,'sale_entries.selling_price as selling_price', 'sale_entries.status as status');
            $entries = $query->get();
            $payments = InPayment::where('sale_id', $saleId)->with('user')->get();
            $res['code'] = 0;
            $res['entries'] = $entries;  
            $res['payments'] = $payments;                          
        } catch(Exception $e) {
            $res['code'] = 1;
            $res['message'] = "Server error";
        }
        return response()->json($res);
    }

    public function cancel(Request $request, $saleId)
    {
        $result = [];
        try{
            DB::transaction(function () use($saleId, &$result, $request){
                $status = $request->query('status');
                $sale = Sale::findOrFail($saleId);
                $sale->status = $status;
                $sale->save();
                Batch::addSaleEntriesBatches($saleId);
                InPayment::where('sale_id', $sale->id)->delete();
                SaleEntry::cancelEntries($sale->id);
                $result['code'] = 0;
            });
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Error";
        }
        return response()->json($result);
    }

    public function getTotalSaleCost($entries)
    {
        $total = 0;
        $receiptEntries = [];
        foreach($entries as $entry) {
            $receitpEntry = [];
            $productId = $entry['product_id'];
            $stockUnitId = $entry['stock_unit_id'];
            $quantity = $entry['quantity'];
            $query = DB::table('product_stock_unit')
                        ->where('product_id', $productId)
                        ->where('stock_unit_id', $stockUnitId)->first();
            $sellingPrice = $query->selling_price;
            $sum = $quantity * $sellingPrice;
            $total += $sum;
            $receitpEntry = ['product'=> Product::findOrFail($productId)->label
                                ,'stock_unit'=> StockUnit::findOrFail($stockUnitId)->label
                                ,'quantity'=> $quantity
                                ,'unit_price'=> $sellingPrice/100
                                ,'sum'=> $sum/100];
            array_push($receiptEntries, $receitpEntry);                    
        }
        return ['total'=> $total, 'receipt_entries'=> $receiptEntries];
    }

    public function createSaleEntries($saleId, $entries)
    {
        foreach($entries as $entry) {
            $productId = $entry['product_id'];
            $product = Product::findOrFail($productId);
            $stockUnitId = $entry['stock_unit_id'];
            $stockUnit = StockUnit::findOrFail($stockUnitId);
            $quantity = $entry['quantity'];
            $productStockUnitQuery = DB::table('product_stock_unit')
                        ->where('product_id', $productId)
                        ->where('stock_unit_id', $stockUnitId)->first();
            $metricScale = $productStockUnitQuery->metric_scale;
            $metricQty = $quantity * $metricScale;            
            $sellingPrice = $productStockUnitQuery->selling_price;
            $costPrice = $productStockUnitQuery->cost_price ?:0;
            $amount = $quantity * $sellingPrice;
            $exp = null;
            $entryBatchD = Batch::removeFirstBatch($productId, $metricQty);
            if(isset($entryBatchD)) {
                $exp = $entryBatchD->expiry_date;
            }
            SaleEntry::create([
                'sale_id'=> $saleId,
                'product_id'=> $productId,
                'product_label'=> $product->label,
                'stock_unit_id'=> $stockUnitId,
                'stock_unit_label'=> $stockUnit->label,
                'selling_price'=> $sellingPrice,
                'cost_price'=> $costPrice,
                'quantity'=> $quantity,
                'metric_quantity'=> $metricQty,
                'amount'=> $amount, 
                'expiry_date'=> $exp
            ]);
            //make deduction
            Product::where('id', $productId)->decrement('stock_quantity', $metricQty);
        }
    }

    public function setBatchNumber($product_id, $quantity)
    {
        $query = DB::table('batches')->where('product_id', $product_id)
                                    ->where('quantity', '>', 0)
                                    ->orderBy('expiry_date', 'ASC')->get();
        $dQty = $quantity;                         
        foreach($query as $b) {
            $id = $b->id;
            $bQuery = DB::table('batches')->where('id', $id);
            $batchData = $bQuery->first();
            $bQty = $batchData->quantity;
            $remaining = $bQty - $dQty;
            $qty = null;
            if($remaining < 0) {
                $dQty = $bQty;
                $qty = $bQty;
                $dQty = -($remaining);
            } else if($remaining == 0) {
                $qty = $bQty;
                $dQty = 0;
            } else if($remaining > 0) {
                $qty = $dQty;
            }
            if($dQty > 0) {
                $bQuery->decrement('quantity', $qty);
            }
        }                           
    }

    public function createPayment($saleId, $userId, $amount)
    {
        InPayment::create([
            'count'=> $amount,
            'user_id'=> $userId,
            'sale_id'=> $saleId
        ]);
    } 
}