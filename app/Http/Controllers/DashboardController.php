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
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:admin,manager');
    }

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
        $dashboard['restock_count'] = $this->restockProductCount();
        $dashboard['expiry_count'] = $this->expiryListCount();
        $dashboard['customer_count'] = count(Customer::all());
        $dashboard['sale_with_debt_count'] = Sale::salesWithDebtCount();
        $dashboard['purchase_with_credit_count'] = Purchase::purchaseWithCreditCount();
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

    public function restockProductCount()
    {
        $products = DB::table('products')
                        ->where('status', 'active')
                        ->whereRaw('products.stock_quantity <= products.reorder_quantity')
                        ->get();
        return count($products);                
    }

    public function expiryListCount() {
        $batches = DB::table('batches')
                        ->leftjoin('products', 'products.id', '=', 'batches.product_id')
                        ->where('batches.expiry_date', '<=', Carbon::now()->addMonths(2))
                        ->select('products.label as product', 'batches.expiry_date as expiry_date'
                                    ,'batches.batch_no as batch_no')
                        ->get();
        return count($batches);                
    }

}
