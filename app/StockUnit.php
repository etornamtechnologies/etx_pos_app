<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 12:25 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Product;

class StockUnit extends Model
{
    protected $table = "stock_units";

    protected $fillable = ['label'];

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_stock_unit')
                    ->withPivot('selling_price', 'metric_scale');
    }
}