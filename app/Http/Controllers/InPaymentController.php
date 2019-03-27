<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\InPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InPaymentController extends Controller
{
    public function store(Request $request)
    {
        $result = [];
        $request->validate([
            'sale_id'=> 'required'
        ]);
        DB::transaction(function () use($request){
            $initTotal = InPayment::totalPaymentForSale($request->get('sale_id'));
            $amount = $request->get('count') *100;
            $totalAmount = $amount + $initTotal;
            Sale::where('id', $request->get('sale_id'))
                ->update(['paid'=> $totalAmount]);
            InPayment::create([
                'sale_id'=> $request->sale_id,
                'count'=> $request->count*100,
                'user_id'=>User::getAuthUser($request)->id,
            ]);
        });
        return response()->json(['code'=> 0, 'message'=> 'payment successfull'], 200); 
    }
}
