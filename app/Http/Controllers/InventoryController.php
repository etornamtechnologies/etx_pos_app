<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 03-Feb-19
 * Time: 8:19 AM
 */

namespace App\Http\Controllers;


use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
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
        // dd(DB::table('product_stock_unit')->where('product_id', $productId));
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
                                                    ->update(['cost_price'=>$costPrice, 'selling_price'=>$sellingPrice]);
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

    public function updateSellingPrice(Request $request, $productId, $stockUnitId)
    {
        //dd($request->all());
        $result = [];
        $request->validate([
            'price'=> 'required'
        ]);
        try {
            DB::table('product_stock_unit')
                ->where('product_id', $productId)
                ->where('stock_unit_id', $stockUnitId)
                ->update(['selling_price'=> $request->price*100]);
            $result['code'] = 0;    
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Could not update selling price";
        }
        return response()->json($result);
    }
}
