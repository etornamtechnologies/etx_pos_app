<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $result = [];
        $result['code'] = 0;
        $dashboard = [];
        $dashboard['today_sale_count'] = Sale::todaySaleCount();
        $dashboard['today_sale_amount'] = Sale::todaySaleAmount();
        $dashboard['product_count'] = Product::totalProducts();
        $result['dashboard'] = $dashboard;
        return response()->json($result, 200);
    }

    public static function dailySale()
    {
        $result = [];
        try {
            $date = Carbon::now();
            $query = Sale::whereDate('created_at', $date)->with(['in_payments']);
            $result['code'] = 0;
            $result['daily_sales'] = $query->get();
        } catch(Esception $e) {
            $result['code'] = 1;
            $result['message'] = "Server error";
        }
        return response()->json($result);
    }


    public function saleTopTenCategories()
    {
        
    }

}
