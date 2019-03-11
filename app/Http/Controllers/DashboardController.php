<?php

namespace App\Http\Controllers;

use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dailySale()
    {
        $result = [];
        try {
            $date = Carbon::now();
            $query = Sale::whereDate('created_at', $date)->with(['in_payments']);
            $result['code'] = 0;
            $result['daily_sales'] = $query->get();
        } catch(Esception $e) {
            $result['code'] = 1;
            $result['message'] = "Server error";
        }
        return response()->json($result);
    }
}
