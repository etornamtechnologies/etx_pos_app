<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 03-Feb-19
 * Time: 8:19 AM
 */

namespace App\Http\Controllers;

use App\Product;
use App\PriceTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role');
    }


    public function index(Request $request)
    {
        return view('inventory.index');
    }

    public function product(Request $request)
    {
        return view('inventory.product');
    }

    public function category(Request $request)
    {
        return view('inventory.category');
    }

    public function manufacturer(Request $request)
    {
        return view('inventory.manufacturer');
    }

    public function stock_unit(Request $request)
    {
        return view('inventory.stock_unit');
    }

    public static function setCostPriceFor($productId, $metricCostPrice)
    {
        DB::table('product_stock_unit')->where('product_id', $productId)
                                        ->orderBy('product_id')
                                        ->get()
                                        ->each(function($stockUnitData, $key) use ($productId, &$metricCostPrice) {
                                            $stockUnitId = $stockUnitData->stock_unit_id;
                                            $metricScale = $stockUnitData->metric_scale;
                                            $costPrice = $metricScale * $metricCostPrice;
                                            DB::table('product_stock_unit')
                                                    ->where('product_id', $productId)
                                                    ->where('stock_unit_id', $stockUnitId)
                                                    ->update(['cost_price'=>$costPrice]);
                                        });

    }

    public static function setSellingPriceFor($productId, $metricCostPrice)
    {
        DB::table('product_stock_unit')->where('product_id', $productId)
                                        ->orderBy('product_id')
                                        ->get()
                                        ->each(function($stockUnitData, $key) use ($productId, &$metricCostPrice) {
                                            $stockUnitId = $stockUnitData->stock_unit_id;
                                            $metricScale = $stockUnitData->metric_scale;
                                            $costPrice = $metricScale * $metricCostPrice;
                                            $sellingPrice = $costPrice*0.10 + $costPrice;
                                            DB::table('product_stock_unit')
                                                    ->where('product_id', $productId)
                                                    ->where('stock_unit_id', $stockUnitId)
                                                    ->update(['selling_price'=>$selling]);
                                        });

    }

    public function addStockUnit(Request $request, $productId, $stockUnitId)
    {
        $result = [];    
        $metricScale = $request->query('metric_scale');
        if($this->productStockUnitExists($productId, $stockUnitId)) {
            $result['code'] = 1;
            $result['message'] = 'stock unit already exists on thie product';
            return response()->json($result);
        }
        try{
            Product::findOrFail($productId)->stock_units()->attach($stockUnitId, ['metric_scale'=> $metricScale]);
            $result['code'] = 0;
            $result['product'] = Product::where('id', $productId)->with(['stock_units', 'category','defaultSku'])->first();
            $result['message'] = "stock unit added";
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function removeStockUnit($productId, $stockUnitId)
    {
        $result = [];
        if(Product::findOrFail($productId)->default_stock_unit == $stockUnitId) {
            $result['code'] = 1;
            $result['message'] = "You cannot remove default stock unit";
        } else {
            try{
                Product::findOrFail($productId)->stock_units()->detach($stockUnitId);
                $result['code'] = 0;
                $result['product'] = Product::where('id', $productId)->with(['stock_units', 'category','defaultSku'])->first();
                $result['message'] = "stock unit removed";
            } catch(Exception $e) {
                $result['code']=1;
                $result['message'] = 'Something went wrong';
            }
        }
        return response()->json($result);
    }

    public static function isStockUnitDefault ($productId, $stockUnitId)
    {
        $stockUnit = DB::table('product_stock_unit')
                            ->where('product_id', $productId)
                            ->where('stock_unit_id', $stockUnitId)
                            ->first();
        if($stockUnit->metric_scale == 1) {
            return true;
        } 
        return false;
    }  

    public static function productStockUnitExists($productId, $stockUnitId)
    {
        $exist = DB::table('product_stock_unit')
                    ->where('product_id', $productId)
                    ->where('stock_unit_id', $stockUnitId)
                    ->first();  
        if($exist) {
            return true;
        }            
        return false;
    }

    public function updateCostPrice(Request $request, $productId, $stockUnitId)
    {
        $input = $request->all();
        DB::table('product_stock_unit')
            ->where('product_id', $productId)
            ->where('stock_unit_id', $stockUnitId)
            ->update(['cost_price'=> $input['cost_price']*100]);
        $product = Product::where('id', $productId)->with(['stock_units', 'category','defaultSku'])->first();
        return response()->json(['code'=> 0, 'message'=>'price updated', 'product'=> $product], 200);
    }

    public function updateStockQuantity(Request $request, $productId)
    {
        $input = $request->all();
        try {
            $product = DB::table('products')
                ->where('id', $productId)
                ->update(['stock_quantity'=> $input['quantity']]);
            return response()->json(['code'=>0, 'message'=> 'Stock Quantity Updated Successful', 'product'=>$product]);
        } catch (Exception $e) {
            return response()->json(['code'=>1, 'message'=>'Error Updating Stock Quantity'], 500);
        }
    }

    public static function updateSellingPrice(Request $request, $productId, $stockUnitId)
    {
        $input = $request->all();
        DB::table('product_stock_unit')
            ->where('product_id', $productId)
            ->where('stock_unit_id', $stockUnitId)
            ->update(['selling_price'=> $input['selling_price']*100]);
        $product = Product::where('id', $productId)->with(['stock_units', 'category','defaultSku'])->first();
        return response()->json(['code'=> 0, 'message'=>'price updated', 'product'=> $product], 200);
    }


    public function applyPriceTemplate(Request $request, $priceTemplateId)
    {
        $priceTemplate = PriceTemplate::findOrFail($priceTemplateId);
        $categoryId = $priceTemplate->category_id;
        $baseValue = $priceTemplate->base_value;
        $percentValue = ($priceTemplate->percent_value/100);
        $constantValue = $priceTemplate->constant_value;
        $applyOn = $priceTemplate->apply_on;
        $stocks = DB::table('product_stock_unit')
                        ->join('products', 'products.id', '=', 'product_stock_unit.product_id')
                        ->when($categoryId, function($query, $categoryId){
                            return $query->where('products.category_id', '=',$categoryId);
                        })
                        ->get();

        foreach($stocks as $stock) {
            $costPrice = $stock->cost_price;
            $sellingPrice = $stock->selling_price;
            $priceValue = 0;
            $applicant = 0;
            $pValue = null;
            if($baseValue == 'cost_price') {
                $pValue = $percentValue*$costPrice;
            } else {
                $pValue = $percentValue*$sellingPrice;
            }
            if($applyOn == 'cost_price') {
                $applicant = $costPrice;
            } else {
                $applicant = $sellingPrice;
            }   
            $pValue = $applicant + $pValue + $constantValue;
            DB::table('product_stock_unit')->where('stock_unit_id', $stock->stock_unit_id)
                                           ->where('product_id', $stock->product_id) 
                                            ->update([
                                                'selling_price'=> $pValue
                                            ]);
        }
        return response()->json(['code'=> 0, 'message'=> 'price template applied']);                
    }
}
