<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('eroles:admin,manager', ['only'=> ['purchasePage', 'listSale', 'listPurchase']]);
    }

    public function createSalePage()
    {
        return view('sale.create');
    }

    public function listSale(Request $request)
    {
        return view('sale.list');
    }

    public function purchasePage()
    {
        return view('purchase.create');
    }

    public function listPurchase(Request $request)
    {
        return view('purchase.list');
    }

    public function createPurchasePage()
    {
        return view('purchase.create');
    }


    public function createAdjustmentPage()
    {
        return view('adjustment.create');
    }

    public function  listAdjustment()
    {
        return view('adjustment.list');
    }
    
}
