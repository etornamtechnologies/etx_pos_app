<?php

namespace App\Http\Controllers;

use App\OutPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OutPaymentController extends Controller
{
    public function store(Request $request)
    {
        $result = [];
        $request->validate([
            'purchase_id'=> 'required'
        ]);
        try {
            OutPayment::create([
                'purchase_id'=> $request->purchase_id,
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
