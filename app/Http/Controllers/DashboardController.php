<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\Product;
use App\Category;
use App\Customer;
use App\Purchase;
use App\Supplier;
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
        $dashboard['total_sale_amount'] = Sale::totalSaleAmount();
        $dashboard['yesterday_total_sale_amount'] = Sale::yesterdayTotalSale();
        $dashboard['this_month_total_sale'] = Sale::thisMonthTotalSale();
        $dashboard['this_month_purchase_total_amount'] = Purchase::thisMonthPurchaseTotalAmount();
        $dashboard['users_count'] = count(User::all());
        $dashboard['customers_count'] = count(Customer::all());
        $dashboard['suppliers_count'] = count(Supplier::all());
        $dashboard['top_category_sale'] = Sale::topCategorySale();
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
