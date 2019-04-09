<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:16 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Category;
use App\Manufacturer;
use App\StockUnit;
use App\Product;

class Product extends Model
{

    protected $table = "products";

    protected $fillable = ['label', 'barcode', 'description', 'category_id', 'manufacturer_id'
                            , 'default_stock_unit', 'status'];

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
}