<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:28 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = "sales";

    protected $fillable = ["customer_id", "reference_number", "total_cost", "user_id", "status"];

    public function entries()
    {
        return $this->hasMany("App\SaleEntry");
    }

    public function user()
    {
        return $this->belongsTo("App\User", "user_id");
    }

    public function customer()
    {
        return $this->belongsTo("App\Customer", "sale_id");
    }

    public function in_payments()
    {
        return $this->hasMany('App\InPayment');
    }

    public function products()
    {
        return $this->hasManyThrough('App\Product', 'App\SaleEntry', 'product_id');
    }
}