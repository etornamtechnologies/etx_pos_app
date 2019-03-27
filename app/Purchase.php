<?php

namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchases";

    protected $fillable = ["supplier_id", "ref_code", "invoice_number"
                                , "total", "user_id", "status", "paid"];

    public function entries()
    {
        return $this->hasMany("App\PurchaseEntry");
    }

    public function user()
    {
        return $this->belongsTo("App\User", "user_id");
    }

    public function supplier()
    {
        return $this->belongsTo("App\Supplier");
    }

    public function out_payments()
    {
        return $this->hasMany('App\OutPayment');
    }

    public static function showPurchase($purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
        $purchase_entries = DB::table('purchase_entries')->where('purchase_id', $purchaseId)
                            ->leftJoin('stock_units', 'purchase_entries.stock_unit_id', '=', 'stock_units.id')
                            ->leftJoin('products', 'purchase_entries.product_id', '=', 'products.id')
                            ->select('purchase_entries.quantity as quantity', 'products.label as product'
                                        ,'stock_units.label as stock_unit', 'purchase_entries.amount as sum'
                                        ,'purchase_entries.cost_price as cost_price')->get();
        return ['purchase'=> $purchase, 'purchase_entries'=> $purchase_entries];                                
    }
}