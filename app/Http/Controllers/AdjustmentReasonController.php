<?php

namespace App\Http\Controllers;

use App\Reason;
use Illuminate\Http\Request;


class AdjustmentReasonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('eroles:manager,admin');
    }

    public function index()
    {
        $result = [];
        try {
            $reasons = Reason::all();
            $result['code'] = 0;
            $result['reasons'] = $reasons;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Server error";
        }
        return response()->json($result);
    }

    public function store(Request $reqeust)
    {
        
    }

    public function createReason(Request $request)
    {
        $request->validate([
            'label'=> 'required|unique:adjustment_reasons'
        ]);
        $result = [];
        try {
            $reason = Reason::create([
                'label'=> strtoupper($request->label)
            ]);
            $result['code'] = 0;
            $result['reason'] = $reason;
        } catch(Exception $e) {
            return $e;
            $result['code'] = 1;
            $result['message'] = "Server error";
        }
        return response()->json($result);
    }
}
