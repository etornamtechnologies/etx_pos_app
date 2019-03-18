<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Adjustment;

class AdjustmentEntry extends Model
{
    protected $table = "adjustment_entries";

    protected $fillable = ["product_id", "stock_unit_id", "adjustment_id"
                                , "old_quantity", "new_quantity", "product_label", "stock_unit_label"];

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
            ]);
            $qty = $stockQty + $entry['difference'];
            $product->stock_quantity = $qty;
            $product->save();
        }
    }
}