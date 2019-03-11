<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:27 AM
 */

namespace App\Http\Controllers;
use App\Sale;
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
    
    public function index(Request $request)
    {
        $result = [];
        $filter = $request->has('filter') ? $request->query('filter') : "";
        try{
            $slaes = [];
            $debts = [];
            if($request->query('type') == 'debts') {
                $query = Sale::where('reference_number', 'LIKE', '%'.$filter.'%')
                                ->with('in_payments')->latest()->get();
                foreach($query as $qq) {
                    $sum = 0;
                    $payments = $qq->in_payments;
                    foreach($payments as $payment) {
                        $sum = $sum + $payment->count;
                    }
                    if($sum < $qq->total_cost) {
                        array_push($debts, Sale::where('id', $qq->id)
                                    ->with(['in_payments', 'user', 'customer'])->first());
                    }
                }
                $result['debts'] = $debts;
                $result['code'] = 0;
            } else {
                $q = Sale::where('reference_number', 'LIKE', '%'.$filter.'%')
                ->orderBy('created_at', 'DES')
                ->with(['user', 'customer','in_payments']);
                if($request->has('paginate')) {
                    $sales = $q->paginate(10);
                } else {
                    $sales = $q->get();
                }               
                $result['code'] = 0;
                $result['sales'] = $sales;
            }
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $result = [];
        $saleCount = Sale::count() + 1;
        $referenceNumber = "LEK-SALE/$saleCount";
        $receiptData = [];
        try{
            DB::transaction(function () use($result, &$referenceNumber, &$receiptData){
                $input = Input::all();
                $userId = Auth::user()->id;
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
                $receiptSummary = ['reference_number'=> $referenceNumber, 'amount_paid'=> $paid/100
                                    ,'total_cost'=> $totalCost/100, 'change'=> $change/100
                                    ,'user'=> Auth::user()->name];
                //create sale
                $sale = Sale::create([
                    'customer_id'=>$customerId,
                    'user_id'=>$userId,
                    'reference_number'=>$referenceNumber,
                    'total_cost'=>$totalCost,
                    'status'=>'active'
                ]);
                //create payment
                $this->createPayment($sale->id, $userId, $amountPaid);
                //createEntries
                $this->createSaleEntries($sale->id, $entries); 
                $receiptData['entries'] = $receiptEntries;
                $receiptData['summary'] = $receiptSummary;
                $receiptData['shop_info'] = Config::findOrFail(1);
            });
            $result['code'] = 0;
            $result['receipt_data'] = $receiptData;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
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
                                        ,'sale_entries.selling_price as selling_price');
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

    public function cancelSale(Request $request, $saleId)
    {
        $result = [];
        try{
            DB::transaction(function () use($saleId, &$result, $request){
                $sale = Sale::findOrFail($saleId);
                $sale->update([
                    'status'=> $request->status
                ]);
                InPayment::where('sale_id', $sale->id)->delete();
                $result['code'] = 0;
                $this->cancelEntries($sale->id);
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
            $stockUnitId = $entry['stock_unit_id'];
            $quantity = $entry['quantity'];
            $productStockUnitQuery = DB::table('product_stock_unit')
                        ->where('product_id', $productId)
                        ->where('stock_unit_id', $stockUnitId)->first();
            $metricScale = $productStockUnitQuery->metric_scale;
            $metricQty = $quantity * $metricScale;            
            $sellingPrice = $productStockUnitQuery->selling_price;
            $costPrice = $productStockUnitQuery->cost_price ?:0;
            $amount = $quantity * $sellingPrice;
            SaleEntry::create([
                'sale_id'=> $saleId,
                'product_id'=> $productId,
                'stock_unit_id'=> $stockUnitId,
                'selling_price'=> $sellingPrice,
                'cost_price'=> $costPrice,
                'quantity'=> $quantity,
                'metric_quantity'=> $metricQty,
                'amount'=> $amount
            ]);
            //make deduction
            BatchController::removeBatch($productId, $metricQty);
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

    public function cancelEntries($saleId)
    {
        $q = DB::table('sale_entries')->where('sale_id', $saleId);
        $entries = $q->get();
        foreach($entries as $entry) {
            SaleEntry::where('id', $entry->id)->update([
                'status'=>'inactive'
            ]);
            $metricQty = $entry->metric_quantity;
            Product::where('id', $entry->product_id)->increment('stock_quantity', $metricScale);
        }
    }
}