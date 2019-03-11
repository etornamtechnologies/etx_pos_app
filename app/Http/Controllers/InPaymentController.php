<?php

namespace App\Http\Controllers;

use App\InPayment;
use Illuminate\Http\Request;
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
        try {
            InPayment::create([
                'sale_id'=> $request->sale_id,
                'count'=> $request->count*100,
                'user_id'=>Auth::user()->id,
            ]);
            $result['code'] = 0;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = 'payment failed!';
        }
        return response()->json($result);
    }
}
