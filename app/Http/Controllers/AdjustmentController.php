<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 1:24 PM
 */

namespace App\Http\Controllers;

use App\User;
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
    public function __contruct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:manager,admin,supervisor');
    }

    public function index(Request $request)
    {
        $stockAdjustments = DB::table('adjustment_entries')
                ->leftjoin('adjustments', 'adjustments.id', '=', 'adjustment_entries.adjustment_id')
                ->leftjoin('users', 'users.id', '=', 'adjustments.user_id')
                ->leftjoin('products', 'adjustment_entries.product_id', '=', 'products.id')
                ->leftjoin('stock_units', 'stock_units.id', '=', 'adjustment_entries.stock_unit_id')
                ->leftjoin('adjustment_reasons', 'adjustment_reasons.id', '=', 'adjustments.reason_id')
                ->select('users.name as user', 'products.label as product', 'stock_units.label as stock_unit'
                            ,'adjustment_entries.old_quantity as ols_quantity', 'adjustment_reasons.label as reason'
                            , 'adjustment_entries.new_quantity as new_quantity', 'adjustments.created_at as created_at'
                            ,DB::raw('adjustment_entries.new_quantity - adjustment_entries.old_quantity as quantity_balance'))
                ->orderBy('adjustments.created_at', 'ASC')            
                ->get();
                return response()->json(['code'=> 0, 'stock_adjustments'=> $stockAdjustments]);
    }

    public function store(Request $request)
    {
        $result = [];
        $request->validate([
            'summary.reason_id'=> 'required'
        ]);
        $token = $request->header('Authorization');
        if(!$token) {
            return response()->json(['message'=> "Not logged in"], 401);
        }
        $adjCount = Adjustment::count() + 1;
        $refCode = "ADJ/".$adjCount;
        try{
            DB::transaction(function () use($result, &$refCode, &$token){
                $input = Input::all();
                $reasonId = $input['summary']['reason_id'];
                $entries = $input['entries'];
                $adjustment = Adjustment::create([
                    'reason_id'=> $reasonId,
                    'ref_code'=> $refCode,
                    'user_id'=> User::AuthUser($token)->id
                ]);
                AdjustmentEntry::createAdjustmentEntries($adjustment->id, $entries);
            });
            $result['code'] = 0;
            $result['message'] = "stock adjustment created successfully";
        } catch (Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Something went wrong";
        }
        return response()->json($result);
    }

}