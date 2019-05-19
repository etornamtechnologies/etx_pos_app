<?php

namespace App;



use App\Batch;
use App\Product;
use App\Purchase;
use App\PurchaseEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\InventoryController;

class PurchaseEntry extends Model
{
    protected $table = "purchase_entries";

    protected $fillable = ["purchase_id", "product_id", "stock_unit_id", "cost_price"
                                , "quantity", "amount", "metric_quantity", "product_label", "stock_unit_label"];

    public function purchase()
    {
        return $this->belongsTo("App\Purchase");
    }

    public function product()
    {
        return $this->belongsTo("App\Product", "product_id");
    }

    public function stock_unit()
    {
        return $this->belongsTo("App\StockUnit", "stock_unit_id");
    }

    public static function createPurchaseEntries($purchase_id, $entries) {
        foreach($entries as $entry) {
            $stockUnitId = $entry['stock_unit_id'];
            $productId = $entry['product_id'];
            $quantity = $entry['quantity'];
            $costPrice = $entry['cost_price']*100;
            $sum = $costPrice * $quantity;
            $stockData = DB::table('product_stock_unit')
                            ->where('product_id', $productId)
                            ->where('stock_unit_id', $stockUnitId)->first();
            $metricScale = $stockData->metric_scale;
            $metricQty = $quantity * $metricScale; 
            Product::where('id', $productId)->increment('stock_quantity', $metricQty);             
            $purchaseEntry = PurchaseEntry::create([
                'purchase_id'=> $purchase_id,
                'product_id'=> $productId,
                'product_label'=> Product::findOrFail($productId)->label,
                'stock_unit_id'=> $stockUnitId,
                'stock_unit_label'=> StockUnit::findOrFail($stockUnitId)->label,
                'cost_price'=> $costPrice,
                'quantity'=> $quantity,
                'amount'=> $sum,
                'metric_quantity'=> $metricQty
            ]);
            //dd($purchaseEntry);
            $metricCostPrice = round($costPrice/$metricScale, 0, PHP_ROUND_HALF_UP);
            InventoryController::setCostPriceFor($productId, $metricCostPrice);
            if($entry['batch_number'] && $entry['expiry_date']) {
                Batch::addProductToBatch($entry['batch_number'], $entry['expiry_date'], $productId, $metricQty);
            }
        }
    }
}