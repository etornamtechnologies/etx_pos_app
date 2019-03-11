<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchases";

    protected $fillable = ["supplier_id", "reference_number", "invoice_number"
                                , "invoice_total_cost", "user_id", "status"];

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
}