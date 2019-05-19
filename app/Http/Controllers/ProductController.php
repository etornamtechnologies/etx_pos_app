<?php

namespace App\Http\Controllers;



use File;
use Excel;
use App\Batch;
use Exception;
use App\Product;
use App\Category;
use App\StockUnit;
use App\helpers\Util;
use App\Manufacturer;
use App\helpers\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\BatchController;

class ProductController extends Controller
{
    public function __construct()
    {
       $this->middleware('api_auth');
       $this->middleware('api_role:supervisor,manager,admin')->except(['index','show']);
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $products = [];
        $filter = "";
        if($request->has('filter')) {
            $filter = $input['filter'];
        }
        if($request->has('pos_search')) {
            $products = Product::where('status', 'active')
                        ->where('label', 'LIKE', '%'.$filter.'%')
                        ->orWhere('barcode', 'LIKE', '%'.$filter.'%')
                        ->orderBy('label', 'ASC')
                        ->with(['category', 'manufacturer', 'stock_units','defaultSku'])
                        ->take(20)
                        ->get();  
            return response()->json(['code'=> 0, 'products'=> $products]);            
        }
        $query = Product::where('label', 'LIKE', '%'.$filter.'%')
                        ->orWhere('barcode', 'LIKE', '%'.$filter.'%')
                        ->orderBy('label', 'ASC')
                        ->with(['category', 'manufacturer', 'stock_units','defaultSku']);               
        if($request->has('paginate')) {
            $products = $query->paginate(10);
        } else {
            $products = $query->get(); 
        }
        return response()->json(['code'=> 0, 'products'=> $products]);
    }

    public function show($id)
    {
        $product = Product::where('id', $id)->with(['stock_units', 'category','defaultSku'])->first();
        $suppliers = Product::getProductSuppliers($id);
        return response()->json(['code'=>0, 'product'=>$product, 'suppliers'=>$suppliers]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'label'=>'required|unique:products',
            'category_id'=> 'required',
            'barcode'=> 'unique:products||nullable',
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
        //dd($input);
        $entries = $input['entries'];
        foreach($entries as $entry) {
            $product = Product::where('label', $entry['label'])->first();
            if(!isset($product)) {
                DB::transaction(function () use($entry){
                    $p = Product::create([
                        'label'=> $entry['label'],
                        'barcode'=> $entry['barcode'],
                        'category_id'=> $entry['category_id'],
                        'default_stock_unit'=> $entry['default_sku_id'],
                        'description'=> $entry['description'],
                        'stock_quantity'=> $entry['quantity'],
                    ]);
                    $productId = $p->id;
                    if($p) {
                        $stockExists = DB::table('product_stock_unit')
                                         ->where('product_id', $p->id)
                                         ->where('stock_unit_id', $entry['default_sku_id'])
                                         ->first();
                        if(!isset($stockExists)) {
                            $p->stock_units()->attach($entry['default_sku_id']
                                                            , ['cost_price'=> $entry['cost_price']*100
                                                            , 'selling_price'=> $entry['selling_price']*100
                                                            , 'metric_scale'=> 1]);
                        }                 
                    }
                    if($entry['batch'] && $entry['expiry_date']) {
                        $existsB = $this->batchExist($entry['batch']);
                        $expiryDateCon = Util::convertDateToCarbon($entry['expiry_date']);
                        if(!isset($existsB)) {
                            $b = Batch::create([
                                'product_id'=> $productId,
                                'quantity'=> $entry['quantity'],
                                'expiry_date'=> $expiryDateCon,
                                'batch_no'=> $entry['batch']
                            ]);
                        }     
                    }
                }); 
            }
        }
        return response()->json(['code'=> 0, 'products created successfully'], 200);
    }

    public function update(Request $request, $productId)
    {
        //dd($request->all());
        $request->validate([
            'label'=>'required|unique:products,label,'.$productId.'id',
            'category_id'=> 'required',
            'barcode'=> 'nullable|unique:products,barcode,'.$productId.'id',
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
                'label'=>strtoupper($request->label),
                'barcode'=>$request->barcode,
                'category_id'=>$request->category_id,
                'manufacturer_id'=>$request->manufacturer_id,
                'description'=>$request->description,
                'default_stock_unit'=>$request->default_stock_unit,
                'reorder_quantity'=>$request->reorder_quantity,
                'status'=> $request->status,
                'stock_quantity'=> $request->stock_quantity
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

    public function storeProductsWithCsv(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);
        if($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {

                })->toArray();
                if(!empty($data)) {
                    foreach($data[0] as $key=>$row) {
                        $entry = [
                            'product' => strtoupper($row["product"]),
                            'category' => strtoupper($row["category"]),
                            'default_uom'=> strtoupper($row['sku']),
                            'stock_quantity' => $row["quantity"],
                            'batch'=> $row['batch'],
                            'expiry_date'=>$row['expiry'],
                            'selling_price' => $row["sp"],
                            'barcode' => $row['barcode'],
                            'cost_price' => $row['cp'],
                            'cost_price' => $row['sp']
                        ];
                        $product = null;
                        $category = null;
                        $categoryId = null;
                        $productId = null;
                        $skuId = null;
                        $sellingPrice = 0;
                        if($entry["product"] && $entry['category'] && $entry['default_uom']) {
                            $existingCat = $this->categoryExist($entry['category']);
                            $existingProduct = $this->productExist($entry['product']);
                            $existingSku = $this->skuExist($entry['default_uom']);
                            if(!isset($existingSku)) {
                                $sk = StockUnit::create(['label'=>$entry['default_uom']]);
                                $skuId = $sk->id;
                            } else {
                                $skuId = $existingSku->id;
                            }
                            if(!isset($existingCat)) {
                                $category = Category::create(['label'=> $entry['category']]);
                                $categoryId = $category->id;
                            } else {
                                $categoryId = $existingCat->id;
                            }
                            if(!isset($existingProduct)) {
                                $product = new Product();
                                $product->default_stock_unit = $skuId;
                                $product->category_id = $categoryId;
                                if($entry['stock_quantity']) {
                                    $product->stock_quantity = $entry['stock_quantity'];
                                }
                            } else {
                                continue;
                            }
                            $product->label = $entry['product'];
                            if($entry['stock_quantity']) {
                                $product->stock_quantity = $entry['stock_quantity'];
                            }
                            if($entry['barcode']) {
                                $product->barcode = $entry['barcode'];
                            }
                            //dd($product);
                            if(!isset($this->productExist)) {
                                $product->save();
                                $productId = $product->id;
                            }
                            if($productId && $skuId) {
                                if($entry['selling_price']) {
                                    $sellingPrice = $entry['selling_price'] * 100;
                                }
                                if($entry['cost_price']) {
                                    $costPrice = $entry['selling_price'] * 100;
                                }
                                if(!$this->productHasSku($productId, $skuId)) {
                                    DB::table('product_stock_unit')->insert([
                                                'product_id'=> $productId, 'stock_unit_id'=> $skuId,
                                                'metric_scale'=>1, 'selling_price'=>$sellingPrice
                                            ]);
                                }
                            }
                            if($entry['batch'] && $entry['expiry_date']) {
                                $existsB = $this->batchExist($entry['batch']);
                                $expiryDateCon = Util::convertDateToCarbon($entry['expiry_date']);
                                if(!isset($existsB)) {
                                    Batch::create([
                                        'product_id'=> $productId,
                                        'quantity'=> $entry['stock_quantity'],
                                        'expiry_date'=> $expiryDateCon,
                                        'batch_no'=> $entry['batch']
                                    ]);
                                }     
                            } 
                        }
                    }
                }
                return response()->json(['code'=> 0, 'message'=> "Products uploaded successfully"]);
            } else {
                $mess = "File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!";
                return response()->json(['code'=> 1, 'message'=> $mess], 200);
            }
        } else {
            $mess = "File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!";
            return response()->json(['code'=> 1, 'message'=> $mess]);
        }
    }

    public function categoryExist($str)
    {
        $cat = Category::where('label', $str)->first();
        return $cat;
    }

    public function productExist($str)
    {
        $cat = Product::where('label', $str)->first();
        return $cat;
    }

    public function skuExist($str)
    {
        $cat = StockUnit::where('label', $str)->first();
        return $cat;
    }

    public function batchExist($str)
    {
        $cat = Batch::where('batch_no', $str)->first();
        return $cat;
    }
    
    public function productHasSku($product_id, $sku_id)
    {
        $cat = DB::table('product_stock_unit')
                        ->where('product_id', $product_id)
                        ->where('stock_unit_id', $sku_id)->first();
        return $cat;
    }
}