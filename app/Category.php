<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:15 PM
 */

namespace App;


use App\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "categories";

    protected $fillable = ['label'];

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public static function topSale()
    {
        $res = DB::table('categories')->leftjoin('products', 'categories.id', 'products.category_id')
                               ->leftjoin('sale_entries', 'sale_entries.product_id', 'products.id')
                               ->select('categories.label as category', 'sale_entries.metric_quantity as quantity')
                               ->get();
        $cats = Category::all();                       
        return $res;                       
    }
}