<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class SaleEntry extends Model
{
    protected $table = "sale_entries";

    protected $fillable = ["sale_id", "product_id", "stock_unit_id", "selling_price"
                            , "cost_price", "quantity", "amount", "metric_quantity", "product_label"
                            ,"stock_unit_label"];

    public function sale()
    {
        return $this->belongsTo("App\Sale");
    }

    public function product()
    {
        return $this->belongsTo("App\Product", "product_id");
    }

    public function stock_unit()
    {
        return $this->belongsTo("App\StockUnit", "stock_unit_id");
    }

    public function user()
    {
        return $this->belongsToManyThrough('App\User', 'App\Sale', 'user_id', 'sale_id');
    }
}