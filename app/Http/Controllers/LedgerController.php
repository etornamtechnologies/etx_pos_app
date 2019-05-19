<?php

namespace App\Http\Controllers;

use App\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LedgerController extends Controller
{
    public function salesWithDebt()
    {
        $q = DB::table('sales')
                ->whereRaw('sales.paid < sales.total_cost')
                ->leftjoin('users', 'users.id', '=', 'sales.user_id')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->select('sales.ref_code as ref_code', 'customers.name as customer', 'sales.created_at as date'
                        , 'sales.total_cost as total_cost', 'sales.paid as amount_paid', 'users.name as user'
                        , DB::raw('sales.total_cost - sales.paid as amount_owed'), 'sales.id as id')
                ->orderBy('sales.created_at', 'DESC');
        $sales = $q->get();                        
        return response()->json(['code'=> 0, 'sales'=> $sales], 200);
    }

    public function purchasesWithCredit()
    {
        $q = DB::table('purchases')
                ->whereRaw('purchases.paid < purchases.total')
                ->leftjoin('users', 'users.id', '=', 'purchases.user_id')
                ->leftjoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                ->select('purchases.ref_code as ref_code', 'suppliers.name as supplier', 'purchases.created_at as date'
                        , 'purchases.total as total_cost', 'purchases.paid as amount_paid', 'users.name as user'
                        , 'purchases.invoice_number as invoice_number', DB::raw('purchases.total - purchases.paid as amount_owing')
                        , 'purchases.id as id')
                ->orderBy('purchases.created_at', 'DESC');
        $purchases = $q->get();                        
        return response()->json(['code'=> 0, 'purchases'=> $purchases], 200);
    }

    public function getAllProductBatches()
    {
            $q = DB::table('batches')
                        ->leftjoin('products', 'products.id', '=', 'batches.product_id')
                        ->leftjoin('stock_units', 'stock_units.id', '=', 'products.default_stock_unit')
                        ->select('products.label as product', 'batches.batch_no as batch', 'batches.created_at as date'
                                ,'batches.quantity as quantity', 'batches.expiry_date as expiry_date'
                                ,'stock_units.label as stock_unit', 'batches.id as id');
        $batches = $q->get();
        return response()->json(['code'=> 0, 'batches'=> $batches], 200);
    }
}
