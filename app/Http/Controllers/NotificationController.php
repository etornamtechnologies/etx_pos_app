<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function getProductRestockList()
    {
        $productQuery = DB::table('products')
                            ->whereRaw('products.stock_quantity <= products.reorder_quantity')
                            ->leftjoin('stock_units', 'stock_units.id', '=', 'products.default_stock_unit')
                            ->select('products.label as product', 'stock_units.label as stock_unit'
                                        ,'products.stock_quantity as quantity', 'products.reorder_quantity as reorder_quantity'
                                        , 'products.barcode as barcode')
                            ->orderBy('products.label', 'ASC');
        $products = $productQuery->get();                
        return response()->json(['code'=> 0, 'products'=> $products], 200);    
    }

    public function getProductExpiryList()
    {
        $batches = DB::table('batches')
                        ->leftjoin('products', 'products.id', '=', 'batches.product_id')
                        ->where('batches.expiry_date', '<=', Carbon::now()->addMonths(2))
                        ->where('batches.quantity', '>', 0)
                        ->select('products.label as product', 'batches.expiry_date as expiry_date'
                                    ,'batches.batch_no as batch_no', 'batches.quantity as quantity')
                        ->get();
        return response()->json(['code'=> 0, 'batches'=> $batches], 200);                            
    }
}
