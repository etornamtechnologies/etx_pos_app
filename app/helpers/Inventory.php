<?php

namespace App\helpers;
use Illuminate\Support\Facades\DB;

    class Inventory 
    {
        public static function isStockUnitBasic($productId, $skuId)
        {
            $sku = DB::table('product_stock_unit')->where('product_id', $productId)
                                                    ->where('stock_unit_id', $skuId)->first();
            if($sku) {
                if($sku->metric_scale == 1) {
                    return true;
                }
            }
            return false;
        }
    }
