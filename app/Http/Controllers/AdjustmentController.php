<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 1:24 PM
 */

namespace App\Http\Controllers;

use App\Batch;
use App\Product;
use App\Adjustment;
use App\AdjustmentEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\BatchController;


class AdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $result = [];
        $filter = "";
        $status = null;
        if($request->has('filter')) {
            $filter = $request->query('filter');
        }
        try{
            $adjustments = Adjustment::where('reference_number', 'LIKE', '%'.$filter.'%')
                ->with('user')->get();
            $result['code'] = 0;
            $result['adjustments'] = $adjustments;
            $status = 200;
        } catch (Exception $e) {
            return $e;
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
            $status = 402;
        }
        return response($result, $status);
    }

    public function store(Request $request)
    {
        $result = [];
        $request->validate([
            'summary.reason_id'=> 'required'
        ]);
        $adjCount = Adjustment::count() + 1;
        $referenceCode = "LEK-ADJ/$adjCount";
        try{
            DB::transaction(function () use($result, &$referenceCode){
                $input = Input::all();
                $reasonId = $input['summary']['reason_id'];
                $entries = $input['entries'];
                $adjustment = Adjustment::create([
                    'reason_id'=> $reasonId,
                    'reference_code'=> $referenceCode,
                    'user_id'=> Auth::user()->id
                ]);
                $this->createAdjustmentEntries($adjustment->id, $entries);
            });
            $result['code'] = 0;
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

    public function createAdjustmentEntries($adjId, $entries)
    {
        foreach($entries as $entry) {
            $productId = $entry['product_id'];
            $product = Product::findOrFail($productId);
            $product = Product::findOrFail($productId);
            // if(Batch::where('product_id', $productId) && !$entry['batch_number'] && !$entry['expiry_date']) {
            //     return false;
            // }
            AdjustmentEntry::create([
                'product_id'=> $productId,
                'stock_unit_id'=> $product->default_stock_unit,
                'old_quantity'=> $entry['old_quantity'],
                'new_quantity'=> $entry['new_quantity'],
                'adjustment_id'=> $adjId
            ]);
            $product->increment('stock_quantity', $entry['difference']);
            if($entry['batch_number'] && $entry['expiry_date']) {
                if($entry['difference'] > 0) {
                    BatchController::addBatch($entry['batch_number'], $entry['expiry_date']
                                                    , $productId, $entry['difference']);
                } else if($entry['difference'] < 0){
                    BatchController::removeBatchFor($productId, $entry['batch_number'], $entry['difference']);
                }
            }
        }
    }
}