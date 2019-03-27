<?php

namespace App\Http\Controllers;



use Exception;
use App\Product;
use App\Category;
use App\StockUnit;
use App\Manufacturer;
use App\helpers\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $filter = "";
        if($request->has('filter')) {
            $filter = $input['filter'];
        }
        $query = Product::where('label', 'LIKE', '%'.$filter.'%')
                        ->orWhere('barcode', 'LIKE', '%'.$filter.'%')
                        ->orderBy('label', 'ASC')
                        ->with(['category', 'manufacturer', 'stock_units','defaultSku']);
        $products = $query->get();                
        return response()->json(['code'=> 0, 'products'=> $products]);
    }

    public function show($id)
    {
        $product = Product::where('id', $id)->with(['stock_units', 'category','defaultSku'])->first();
        return response()->json(['code'=>0, 'product'=>$product]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'label'=>'required|unique:products',
            'category_id'=> 'required',
            'barcode'=> 'unique:products',
            'default_stock_unit'=> 'required'
        ]);
        $result = [];
        $status = null;
        try{
            $product = Product::create([
                'label'=>strtoupper($request->label),
                'barcode'=>$request->barcode,
                'category_id'=>$request->category_id,
                'manufacturer_id'=>$request->manufacturer_id,
                'description'=>$request->description,
                'default_stock_unit'=>$request->default_stock_unit,
                'stock_quantity'=>0,
            ]);
            $product->stock_units()->attach($request->default_stock_unit, ['metric_scale'=>1]);
            $result['code'] = 0;
            $result['product'] = Product::where('id',$product->id)->with(['category','stock_units','defaultSku'])->first();
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result, $status);
    }

    public function storeEntries(Request $request)
    {
        $input = $request->all();
        $entries = $input['entries'];
        foreach($entries as $entry) {
            $product = Product::where('label', $entry['label'])->first();
            if(!isset($product)) {
                $p = Product::create([
                    'label'=> $entry['label'],
                    'barcode'=> $entry['barcode'],
                    'category_id'=> $entry['category_id'],
                    'default_stock_unit'=> $entry['default_sku_id'],
                    'description'=> $entry['description'],
                ]);
                $p->stock_units()->attach($entry['default_sku_id'], ['metric_scale'=> 1]);
            }
        }
        return response()->json(['code'=> 0, 'products created successfully'], 200);
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'label'=>'required|unique:products,label,'.$productId.'id',
            'category_id'=> 'required',
            'barcode'=> 'unique:products,barcode,'.$productId.'id'
        ]);
        $product = Product::findOrFail($productId);
        $result = [];
        try{
            if($request->has('default_stock_unit')) {
                if(!Inventory::isStockUnitBasic($productId, $request->default_stock_unit)){
                    $result['code'] = 1;
                    $result['message'] = "You can only set stock units with metric scale 1 as default";
                    return response()->json($result);  
                }
            } else {
                $result['code'] = 1;
                $result['message'] = "Please default stock unit cannot be empty";
                return response()->json($result);
            }
            $product->update([
                'name'=>strtoupper($request->label),
                'barcode'=>$request->barcode,
                'category_id'=>$request->category_id,
                'manufacturer_id'=>$request->manufacturer_id,
                'description'=>$request->description,
                'default_stock_unit'=>$request->default_stock_unit,
                'reorder_quantity'=>$request->reorder_quantity,
                'status'=> $request->status,
            ]);
            $result['code'] = 0;
            $result['product'] = $product;
            $result['message'] = "product updated successfully";
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function destroy($productId)
    {
        $result = [];
        $status = null;
        try{
            Product::destroy($productId);
            $result['code'] = 0;
            $result['message'] = "product deleted successfully";
            $status = 200;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status=401;
        }
        return response()->json($result);
    }
}