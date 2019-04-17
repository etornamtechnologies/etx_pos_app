<?php

namespace App\Http\Controllers;

use File;
use Excel;
use Session;
use App\Role;
use App\User;
use App\Batch;
use App\Product;
use App\Category;
use App\StockUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:admin');
    }

    public function indexPage()
    {
        return view('admin.index');
    }

    public function index(Request $request)
    {
        $users = User::all();
        return response()->json(['code'=> 0, 'users'=> $users], 200);
    }

    public function userPage()
    {
        return view('admin.user');
    }

    public function show(Request $request, $id)
    {
        $user = User::where('id', $id)->with(['roles'])->first();
        return response()->json(['code'=> 0, 'user'=> $user]);
    }

    public function assignRole(Request $request, $id)
    {
        DB::transaction(function () use($request) {
            $input = $request->all();
            $userId = $input['id'];
            $myuser = User::findOrFail($userId);
            $myuser->roles()->detach();
            if($request->has('sales_rep')) {
                $myuser->roles()->attach(Role::where('label', 'sales-rep')->first());
            }
            if($request->has('manager')) {
                $myuser->roles()->attach(Role::where('label', 'manager')->first());
            }
            if($request->has('admin')) {
                $myuser->roles()->attach(Role::where('label', 'admin')->first());
            }
        });
        $user = User::where('id', $id)->with(['roles'])->first();
        return response()->json(['code'=>0, 'user'=> $user, 'message'=> 'user roles updated'], 200);
    }

    public function loadCsvPage()
    {
        return view('admin.csv');
    }

    public function loadcsv(Request $request)
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
                    foreach($data[0] as $key=>$entry) {
                        $product = null;
                        $category = null;
                        $categoryId = null;
                        $productId = null;
                        $skuId = null;
                        $sellingPrice = null;
                        $quantity = 0;
                        if($entry["product"] && $entry['category'] && $entry['default_uom']) {
                            $existingCat = $this->categoryExist($entry['category']);
                            $existingProduct = $this->productExist($entry['product']);
                            $existingSku = $this->skuExist($entry['default_uom']);
                            if(!isset($existingSku)) {
                                $sku = StockUnit::create(['label'=>$entry['default_uom']]);
                                $skuId = $sku->id;
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
                                $product->default_uom = $skuId;
                                $product->categories_id = $categoryId;
                                if($entry['stock_quantity']) {
                                    $product->stock_quantity = $entry['stock_quantity'];
                                }
                            } else {
                                continue;
                            }
                            $product->name = $entry['product'];
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
                                    $sellingPrice = $entry['selling_price']*100;
                                }
                                if(!isset($this->productHasSku)) {
                                    DB::table('product_stock_unit')->insert(
                                        ['products_id'=> $productId, 'stock_units_id'=> $skuId
                                            , 'metric_scale'=>1, 'selling_price'=>$sellingPrice]
                                    );
                                }
                            }
                        }
                    }
                }
                Session::flash('success', 'File loaded successfully');
                return redirect()->back();
            } else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return redirect()->back();
            }
        } else {
            Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
            return redirect()->back();
        }        
    }

    public function categoryExist($str)
    {
        $cat = Category::where('label', $str)->first();
        return $cat;
    }

    public function productExist($str)
    {
        $cat = Product::where('name', $str)->first();
        return $cat;
    }

    public function skuExist($str)
    {
        $cat = StockUnit::where('label', $str)->first();
        return $cat;
    }

    public function batchExist($str)
    {
        $bat = Batch::where('label', $str)->first();
        return $bat;
    }
    
    public function productHasSku($product_id, $sku_id)
    {
        $cat = DB::table('product_stock_unit')
                        ->where('products_id', $product_id)
                        ->where('stock_units_id', $sku_id)->first();
        return $cat;
    }

    public function createBackup() 
    {
        Artisan::call('backup:run');
        return response()->json(['code'=> 0, 'message'=> 'backup created successfully']);
    }

    public function restoreBackup() 
    {
        //Artisan::call('backup:run');
        return response()->json(['code'=> 0, 'message'=> 'backup created successfully']);
    }
}
