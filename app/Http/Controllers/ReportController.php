<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\Purchase;
use App\SaleEntry;
use Carbon\Carbon;
use App\PurchaseEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('api_auth');
        $this->middleware('api_role:manager,admin');
    }

    public function indexPage()
    {
        return view('report.index');
    }

    public function salePage()
    {
        return view('report.sale');
    }

    public function purchasePage()
    {
        return view('report.purchase');
    }

    public function adjustmentPage()
    {
        return view('report.adjustment');
    }


    public function getReport(Request $request)
    {   
        $res = [];
        $input = $request->all();
        $invoiceType = $input['type'];
        $reportBy = $input['by'];
        $date = $input['date'];
        $referenceNumber = $input['reference_number'];
        $product = $input['product'];
        $fromDate = $input['from_date'];
        $toDate = $input['to_date'];
        $isRange = $input['is_range'];
        $user = $input['user'];

        $duration = "";
        $messageUser = "all users";
        $messageReportType = "";
        $messageBy = "";
        if($invoiceType == 'sale') {
            $messageReportType = "sale report";
        } else if($invoiceType == 'purchase') {
            $messageReportType = "purchase report";
        } else {
            $messageReportType = "adjustment report";
        }
        if($input['user']) {
            $messageUser = User::findOrFail($user)->name;
        }
        if($input['by'] == 'transaction') {
            $messageBy = "per transaction";
        } else {
            $messageBy = "per product";
        }
        if($isRange == 'true') {
            $duration = "from ".$fromDate." to ".$toDate;
        } else {
            $duration = "on ".$date;
        }
        $message = $messageReportType.' '.$messageBy.' for '.$messageUser." ".$duration;
        $result = [];
        try {
            $res = [];
            if($invoiceType == 'sale') {
                if($reportBy == 'transaction') {
                    $res = $this->getSaleReportByTransaction($input);
                } else {
                    $res = $this->getSaleReportByProduct($input);
                }
            } else if($invoiceType == 'purchase') {
                if($reportBy == 'transaction') {
                    $res = $this->getPurchaseReportByTransaction($input);
                } else {
                    $res = $this->getPurchaseReportByProduct($input);
                }
            }
            $result['code'] = 0;
            $result['report_info'] = $message;
            $result['report_data'] = $res;
        } catch(Exception $e) {
            $result['code'] = 1;
            $result['message'] = "Server Error";
        }
        return response()->json($result);
    }

    public function getSaleReportByTransaction(Request $request)
    {

        $input = $request->all();
        //dd($input);
        $isRange = $input['is_date_range'];
        $reportBy = $input['filter_by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $toDate = $input['to_date'];
        $product = $input['product'];
        $referenceNumber = $input['invoice_number'];
        $userId = $input['user_id'];
        $info = "sale report by transaction";
        $user = " sold by all sales reps";
        $duration = "";
        $fil = " for all transactions ";
        if($userId) {
            $user = " sold by ".User::findOrFail($userId)->name;
        }
        if($referenceNumber) {
            $fil = " for invoice number ".$referenceNumber;
        }
        if(!$isRange && $date) {
            $duration = " on ".$date;
        }
        if($isRange) {
            $duration = " from ".$fromDate." to ".$toDate;
        }
        $reportQuery = Sale::when($userId, function($query, $userId) use($user) {
                            return $query->where('sales.user_id', $userId);
                        })
                        ->when($referenceNumber, function($query, $referenceNumber){
                            return $query->where('sales.ref_code', 'LIKE', '%'.$referenceNumber.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('sales.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->whereDate('sales.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->whereDate('sales.created_at', '<=', $d);
                        })
                        ->where('sales.status', '=', 'active')
                        ->with(['in_payments','customer', 'user', 'entries']);
        $totalAmount = $reportQuery->sum('total_cost');
        $totalPaid = $reportQuery->sum('paid');                
        $reports = $reportQuery->get();
        $info = $info.$fil.$user.$duration;
        $header = [['value'=>'reference_number', 'text'=>'reference_number'], ['value'=> 'created_at', 'text'=> 'date']
                    , ['value'=> 'user', 'text'=> 'sales rep'], ['value'=> 'customer', 'text'=> 'customer']
                    , ['value'=> 'total_cost', 'text'=> 'invoice total'], ['value'=>'paid', 'text'=> 'amount paid']];
        return response()->json(['code'=> 0, 'reports'=> $reports, 'headers'=> $header
                                    , 'info'=>$info, 'total_amount'=> $totalAmount, 'total_paid'=> $totalPaid]);   
    }

    public function getSaleReportByProduct(Request $request)
    {
        $input = $request->all();
        $isRange = $input['is_date_range'];
        $reportBy = $input['filter_by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['invoice_number'];
        $userId = $input['user_id'];
        $toDate = $input['to_date'];
        $info = "sale report by product ";
        $user = " sold by all users ";
        $duration = "";
        $fil = "for all products";
        if($userId) {
            $user = " sold by ".User::findOrFail($userId)->name;
        }
        if($product) {
            $fil = "for product ".$product;
        }
        if(!$isRange && $date) {
            $duration = " on ".$date;
        }
        if($isRange) {
            $duration = " from ".$fromDate." to ".$toDate;
        }
        $reportQuery = SaleEntry::when($product, function($query, $product){
                            return $query->join('products', 'products.id', '=', 'sale_entries.product_id')
                                            ->where('products.label', 'LIKE', '%'.$product.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('sale_entries.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->whereDate('sale_entries.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->whereDate('sale_entries.created_at', '<=', $d);
                        })
                        ->where('sale_entries.status', '=', 'active')
                        ->with(['sale', 'product', 'stock_unit']);
        $reports = $reportQuery->get();
        $totalAmount = $reportQuery->sum('amount');
        $header = [['value'=> 'product.label', 'text'=> 'product'], ['value'=>'created_at', 'text'=>'date']
                    ,['value'=> 'quantity', 'text'=> 'quantity'], ['value'=> 'selling_price', 'text'=> 'selling-price']
                    ,['value'=> 'amount', 'text'=>'sub-total']];
        $info = $info.$fil.$user.$duration;
        return response()->json(['code'=> 0, 'reports'=> $reports, 'info'=> $info, 'header'=> $header, 'total_amount'=> $totalAmount], 200);
    }

    public function getPurchaseReportByTransaction(Request $request)
    {
        $input = $request->all();
        //dd($input);
        $isRange = $input['is_date_range'];
        $reportBy = $input['filter_by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['invoice_number'];
        $userId = $input['user_id'];
        $toDate = $input['to_date'];
        $info = "purchase report by transaction";
        $user = " entered by all users";
        $duration = "";
        $fil = " for all transactions ";
        if($userId) {
            $user = "entered by ".User::findOrFail($userId)->name;
        }
        if($referenceNumber) {
            $fil = " for invoice number ".$referenceNumber;
        }
        if(!$isRange && $date) {
            $duration = " on ".$date;
        }
        if($isRange) {
            $duration = " from ".$fromDate." to ".$toDate;
        }
        $reportQuery = Purchase::when($userId, function($query, $userId){
                            return $query->where('purchases.user_id', $userId);
                        })
                        ->when($referenceNumber, function($query, $referenceNumber){
                            return $query->where('purchases.ref_code', 'LIKE', '%'.$referenceNumber.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('purchases.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->whereDate('purchases.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->whereDate('purchases.created_at', '<=', $d);
                        })
                        ->with(['out_payments', 'supplier', 'user']);
        $reports = $reportQuery->get();
        $totalAmount = $reportQuery->sum('total');
        $totalPaid = $reportQuery->sum('paid');
        $headers = [['value'=>'ref_code', 'text'=>'reference number'], ['value'=> 'created_at', 'text'=> 'date']
                    , ['value'=> 'user', 'text'=> 'sales rep'], ['value'=> 'supplier', 'text'=> 'supplier']
                    , ['value'=> 'invoice_total_cost', 'text'=> 'invoice total cost']
                    , ['text'=>'amount paid', 'value'=>'amount_paid']];
        $info = $info.$fil.$user.$duration;
        return response()->json(['code'=>0,'reports'=> $reports, 'headers'=> $headers
                                , 'info'=> $info, 'total_amount'=> $totalAmount, 'total_paid'=> $totalPaid]);         
    }


    public function getPurchaseReportByProduct(Request $request)
    {
        $input = $request->all();
        $isRange = $input['is_date_range'];
        $reportBy = $input['filter_by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['invoice_number'];
        $userId = $input['user_id'];
        $toDate = $input['to_date'];
        $info = "purchase report by transaction";
        $user = " entered by all users";
        $duration = "";
        $fil = " for all transactions ";
        if($userId) {
            $user = "entered by ".User::findOrFail($userId)->name;
        }
        if($referenceNumber) {
            $fil = " for invoice number ".$referenceNumber;
        }
        if(!$isRange && $date) {
            $duration = " on ".$date;
        }
        if($isRange) {
            $duration = " from ".$fromDate." to ".$toDate;
        }
        $reportQuery = PurchaseEntry::when($product, function($query, $product){
                            return $query->join('products', 'products.id', '=', 'purchase_entries.product_id')
                                            ->where('products.label', 'LIKE', '%'.$product.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('purchase_entries.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->whereDate('purchase_entries.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->whereDate('purchase_entries.created_at', '<=', $d);
                        })
                        ->with(['purchase', 'product', 'stock_unit']);
        $reports = $reportQuery->get();
        $info = $info.$fil.$user.$duration;
        $totalAmount = $reportQuery->sum('amount');
        $header = [['value'=> 'product.label', 'text'=> 'product'], ['value'=>'created_at', 'text'=>'date']
                    ,['value'=> 'quantity', 'text'=> 'quantity']
                    , ['value'=> 'cost_price', 'text'=> 'cost-price'], ['value'=> 'amount', 'text'=> 'amount']];
        return response()->json(['code'=> 0, 'reports'=> $reports, 'header'=>$header, 'info'=> $info
                                    ,'total_amount'=> $totalAmount], 200);
    }

    public function getFinancialReport(Request $request)
    {
        $input = $request->all();
        $fromDate = new Carbon($input['from_date']);
        $toDate = new Carbon($input['to_date']);
        $duration = "from ".$fromDate." to ".$toDate;
        $result = [];
        $result['code'] = 0;
        $result['sale_profit'] = $this->getSaleProfit($fromDate, $toDate);
        $result['total_sale'] = $this->totalSale($fromDate, $toDate);
        $result['info'] = "Financial report from ".$fromDate." to ".$toDate;
        $result['total_purchase'] = $this->totalPurchase($fromDate, $toDate);
        return response()->json($result, 200);
    }

    public function getSaleProfit($fromDate, $toDate)
    {
        $q = DB::table('sale_entries')
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=' , $toDate)
                ->select('sale_entries.cost_price as cp', 'sale_entries.selling_price as sp'
                        ,'sale_entries.quantity as quantity')
                ->get();
        $profit = 0;
        foreach ($q as $sale) {
            $cp = $sale->cp;
            $sp = $sale->sp;
            $margin = $sp-$cp;
            $qty = $sale->quantity;
            $sum = $qty*$margin;
            $profit += $sum;
        }        
        return $profit;
    }

    public function totalSale($fromDate , $toDate)
    {
        $q = DB::table('sales')
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=' , $toDate)
                ->select('sales.total_cost as total_cost', 'sales.paid as paid')
                ->get();
        $totalCost = 0;
        $totalPaid = 0;        
        foreach ($q as $sale) {
            $cost = $sale->total_cost;
            $paid = $sale->paid;
            $totalCost += $cost;
            $totalPaid += $paid;
        }       
        $amountOwed = $totalCost - $totalPaid;
        return ['total_cost'=> $totalCost, 'total_paid'=> $totalPaid, 'amount_owed'=> $amountOwed];
    }

    public function totalPurchase($fromDate , $toDate)
    {
        $q = DB::table('purchases')
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=' , $toDate)
                ->select('purchases.total as total_cost', 'purchases.paid as paid')
                ->get();
        $totalCost = 0;
        $totalPaid = 0;        
        foreach ($q as $sale) {
            $cost = $sale->total_cost;
            $paid = $sale->paid;
            $totalCost += $cost;
            $totalPaid += $paid;
        }       
        $amountOwed = $totalCost - $totalPaid;
        return ['total_cost'=> $totalCost, 'total_paid'=> $totalPaid, 'amount_owed'=> $amountOwed];
    }
}
