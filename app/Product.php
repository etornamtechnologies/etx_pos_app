<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:16 PM
 */

namespace App;


use App\Product;
use App\Category;
use App\StockUnit;
use App\Manufacturer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $table = "products";

    protected $fillable = ['label', 'barcode', 'description', 'category_id', 'manufacturer_id'
                            , 'default_stock_unit', 'status', 'reorder_quantity', 'stock_quantity'];

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo('App\Manufacturer', 'manufacturer_id');
    }

    public function stock_units()
    {
        return $this->belongsToMany('App\StockUnit', 'product_stock_unit')
            ->withPivot('selling_price', 'metric_scale', 'cost_price');
    }

    public function defaultSku()
    {
        return $this->belongsTo('App\StockUnit', 'default_stock_unit');
    }

    public static function totalProducts()
    {
        return Product::count();
    }

    public static function getProductSuppliers($productId)
    {
        $q = DB::table('purchase_entries')->where('product_id', $productId)
                ->leftjoin('purchases', 'purchases.id', '=', 'purchase_entries.purchase_id')
                ->leftjoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->distinct()
                ->select('suppliers.name as supplier_name', 'suppliers.phone as supplier_phone'
                            ,'suppliers.email as supplier_email', 'suppliers.address as supplier_address')
                ->get();
        return $q;        
    }
}