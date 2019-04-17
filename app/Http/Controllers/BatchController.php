<?php

namespace App\Http\Controllers;

use App\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BatchController extends Controller
{

    public function __contruct()
    {
        $this->middleware('api_auth');
    }

    public static function addBatch($batch_number, $expiry_date, $productId, $metricQty)
    {
        $expiryDate = null;
        $batchNumber = $batch_number;
        try {
            $dateData = explode('/', $expiry_date);
            $month = $dateData[0];
            $year = $dateData[1];
            $expiryDate = Carbon::createFromDate($year, $month, 1);
        } catch(\Carbon\Exceptions\InvalidDateException $e) {
            return response()->json(['code'=>1, 'message'=>$e->getMessage()]);
        }
        $batchesQuery = Batch::where('product_id', $productId)
                        ->where('label', $batchNumber);
        if($batchesQuery->first()) {
            $batchesQuery->increment('quantity', $metricQty);   
        }   else {
            Batch::create([
                'product_id'=> $productId,
                'label'=> $batchNumber,
                'expiry_date'=> $expiryDate,
                'quantity'=> $metricQty
            ]);
        }
    }


    public static function removeBatch($product_id, $quantity)
    {
        Batch::where('quantity', 0)->delete();
        $query = DB::table('batches')->where('product_id', $product_id)
                                    ->where('quantity', '>', 0)
                                    ->orderBy('expiry_date', 'ASC')->get();
        $dQty = $quantity;                         
        foreach($query as $b) {
            $id = $b->id;
            $bQuery = DB::table('batches')->where('id', $id);
            $batchData = $bQuery->first();
            $bQty = $batchData->quantity;
            $remaining = $bQty - $dQty;
            $qty = null;
            if($remaining < 0) {
                $dQty = $bQty;
                $qty = $bQty;
                $dQty = -($remaining);
            } else if($remaining == 0) {
                $qty = $bQty;
                $dQty = 0;
            } else if($remaining > 0) {
                $qty = $dQty;
            }
            if($dQty > 0) {
                $bQuery->decrement('quantity', $qty);
            }
        }  
    }

    public function removeBatchFor($productId, $batchNumber, $quantity)
    {
        $batches = Batches::where('product_id', $productId)->where('label', $batchNumber)->first();
        if($batches) {
            $batches->decrement('quantity', $quantity);
        } else {
            $this->removeBatch($productId, $quantity);
        }
    }

    public function deductOldBatch()
    {
        $batches = Batch::orderBy();
    }
}
