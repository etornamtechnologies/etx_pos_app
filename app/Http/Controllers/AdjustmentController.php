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
        $this->middleware('api_role:manager,admin');
    }

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