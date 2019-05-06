<?php

namespace App;


use App\Batch;
use App\Product;
use App\StockUnit;
use App\Adjustment;
use App\AdjustmentEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AdjustmentEntry extends Model
{
    protected $table = "adjustment_entries";

    protected $fillable = ["product_id", "stock_unit_id", "adjustment_id"
                            , "old_quantity", "new_quantity", "product_label"
                            , "stock_unit_label", "cost_price", "selling_price"];

    public function adjsutment()
    {
        return $this->belongsTo("App\Adjustment", "adjustment_id");
    }

    public static function createAdjustmentEntries($adjId, $entries)
    {
        foreach($entries as $entry) {
            $productId = $entry['product_id'];
            $product = Product::findOrFail($productId);
            $stockUnit = StockUnit::findOrFail($product->default_stock_unit);
            $productStockData = DB::table('product_stock_unit')
                                ->where('product_id', $productId)
                                ->where('stock_unit_id', $product->default_stock_unit)
                                ->first();
            $stockQty = $product->stock_quantity;
            if($entry['batch_number'] && $entry['expiry_date']) {
                Batch::addProductToBatch($entry['batch_number'], $entry['expiry_date'], $productId, $entry['difference']); 
            }
            AdjustmentEntry::create([
                'product_id'=> $productId,
                'stock_unit_id'=> $product->default_stock_unit,
                'old_quantity'=> $entry['old_quantity'],
                'new_quantity'=> $entry['new_quantity'],
                'adjustment_id'=> $adjId,
                'product_label'=> $product->label,
                'stock_unit_label'=> $stockUnit->label,
                'cost_price'=> $productStockData->cost_price,
                'selling_price'=> $productStockData->selling_price,
            ]);
            $qty = $stockQty + $entry['difference'];
            $product->stock_quantity = $qty;
            $product->save();
        }
    }
}