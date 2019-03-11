<?php

namespace App;



use App\Purchase;
use Illuminate\Database\Eloquent\Model;

class PurchaseEntry extends Model
{
    protected $table = "purchase_entries";

    protected $fillable = ["purchase_id", "product_id", "stock_unit_id", "cost_price"
                                , "quantity", "amount", "metric_quantity"];

    public function purchase()
    {
        return $this->belongsTo("App\Purchase");
    }

    public function product()
    {
        return $this->hasOne("App\Product", "product_id");
    }

    public function stock_unit()
    {
        return $this->hasOne("App\StockUnit", "stock_unit_id");
    }
}