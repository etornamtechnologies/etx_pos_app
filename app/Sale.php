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
use Illuminate\Support\Facades\DB;
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

    public static function totalSaleAmount()
    {
        $sales = Sale::all();
        $total = 0;
        foreach($sales as $sale) {
            $amt = $sale->total_cost;
            $total += $amt;
        }
        return $total;
    }

    public static function yesterdayTotalSale()
    {
        $yesterday = Carbon::yesterday();
        $sales = Sale::where('status', 'active')->whereDate('created_at',$yesterday)->get();
        $total = 0;
        foreach($sales as $sale) {
            $amt = $sale->total_cost;
            $total += $amt;
        }
        return $total;
    }

    public static function thisMonthTotalSale()
    {
        $sales = Sale::where('status', 'active')->whereMonth('created_at', Carbon::now()->month)->get();
        $total = 0;
        foreach($sales as $sale) {
            $amt = $sale->total_cost;
            $total += $amt;
        }
        return $total;
    }

    public static function topCategorySale()
    {
        $q = DB::table('categories')
                ->leftjoin('products', 'products.category_id', '=', 'categories.id')
                ->leftjoin('sale_entries', 'sale_entries.product_id', '=', 'products.id')
                ->select('categories.label as category', DB::raw('SUM(sale_entries.metric_quantity) as quantity'))
                ->groupBy('categories.id')
                ->orderBy(DB::raw('SUM(sale_entries.metric_quantity)'), 'DESC')
                ->take(10)
                ->get();
        return $q;        
    }

    public static function mondaySale()
    {
        $monday = Carbon::now()->monday;
        dd($monday);
    }
}