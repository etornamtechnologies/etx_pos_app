<?php

namespace App\Http\Controllers;

use App\User;
use App\Purchase;
use App\OutPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OutPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:admin,manager')->except(['index']);
    }

    public function store(Request $request)
    {   

        $result = [];
        $request->validate([
            'purchase_id'=> 'required'
        ]);
        DB::transaction(function () use($request){
            $initTotal = OutPayment::totalPaymentForPurchase($request->get('purchase_id'));
            $amount = $request->get('count') *100;
            $totalAmount = $amount + $initTotal;
            Purchase::where('id', $request->get('purchase_id'))
                ->update(['paid'=> $totalAmount]);
            OutPayment::create([
                'purchase_id'=> $request->purchase_id,
                'count'=> $request->count*100,
                'user_id'=>User::getAuthUser($request)->id,
            ]);
        });
        return response()->json(['code'=> 0, 'message'=> 'payment successfull'], 200);
    }
}
