<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:28 AM
 */

namespace App;


use App\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = "sales";

    protected $fillable = ["customer_id", "ref_code", "total_cost", "user_id", "status", "paid"];

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
        return $this->belongsTo("App\Customer", "customer_id");
    }

    public function in_payments()
    {
        return $this->hasMany('App\InPayment');
    }

    public function products()
    {
        return $this->hasManyThrough('App\Product', 'App\SaleEntry', 'product_id');
    }

    public static function todaySaleCount()
    {
        $date = Carbon::now();
        $query = Sale::whereDate('created_at', $date)->get();
        return count($query);
    }

    public static function todaySaleAmount()
    {
        $date = Carbon::now();
        $sales = Sale::whereDate('created_at', $date)->get();
        $total = 0;
        foreach($sales as $sale) {
            $total = $total + $sale->total_cost;
        }
        return $total;
    }

    public static function topSales()
    {
        //$query = DB::table('sales')->join()
    }
}