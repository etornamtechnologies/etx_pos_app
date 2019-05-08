<?php

namespace App;


use App\Batch;
use Carbon\Carbon;
use App\helpers\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "batches";

    protected $fillable = ["product_id", "quantity", "batch_no", "expiry_date"];

    public static function addProductToBatch($batch_no, $expiry_date, $product_id, $quantity)
    {
        $batch = Batch::where('product_id', $product_id)->where('batch_no', $batch_no)->first();
        if(!$batch) {
            $dd = Util::convertDateToCarbon($expiry_date);
            Batch::create([
                'batch_no'=> $batch_no,
                'product_id'=> $product_id,
                'expiry_date'=> $dd,
                'quantity'=> $quantity
            ]);
        } else {
            $bqty = $batch->quantity;
            $qty = $bqty + $quantity;
            $batch->update([
                'quantity'=> $qty
            ]);
        }
    }

    public static function removeFirstBatch($product_id, $quantity)
    {
        Batch::where('quantity', 0)->delete();
        $query = DB::table('batches')->where('product_id', $product_id)
                                    ->where('quantity', '>', 0)
                                    ->orderBy('expiry_date', 'ASC')->get();  
        $expiryDateForEntry = $query->first();                                                 
        $remainingQty = $quantity;
        $done = 0;                
        foreach($query as $b) {
            $batchData = Batch::findOrFail($b->id);
            $bQty = $batchData->quantity;
            $deltaQty = $bQty - $remainingQty;
            if($deltaQty >= 0) {
                $q = $batchData->quantity-$remainingQty;
                $batchData->update(['quantity' => $q]);
                $remainingQty = 0;
                $done = 1;
            } else {
                $q = 0;
                $remainingQty = $remainingQty-$bQty;
                $batchData->update(['quantity' => $q]);
            }
        }
        return $expiryDateForEntry;
    }

    public static function addLastBatch($product_id, $quantity) {
        $batches = Batch::where('product_id', $product_id)
                      ->orderBy('expiry_date', 'DESC')
                      ->get();
        $lastBatch = $batches[0];
        if($lastBatch) {
            Batch::where('batch_no', $lastBatch['batch_no'])
                ->increment('quantity', $quantity);
        }              
    }

    public static function addSaleEntriesBatches($saleId)
    {
        $q = DB::table('sale_entries')->where('sale_id', $saleId);
        $entries = $q->get();
        foreach($entries as $entry) {
            $isExpiryDateExist = $entry->expiry_date;
            if($isExpiryDateExist) {
                $qty = $entry->metric_quantity;
                $batch = Batch::where('expiry_date', $entry->expiry_date)->increment('quantity', $qty);
            }
        }
    }
}