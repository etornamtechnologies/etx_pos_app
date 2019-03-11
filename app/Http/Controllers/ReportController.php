<?php

namespace App\Http\Controllers;

use App\Sale;
use App\User;
use App\Purchase;
use App\SaleEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    public function getSaleReportByTransaction($input)
    {
        $invoiceType = $input['type'];
        $isRange = $input['is_range'];
        $reportBy = $input['by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['reference_number'];
        $user = $input['user'];
        $toDate = $input['to_date'];
        $reportQuery = Sale::when($user, function($query, $user){
                            return $query->where('sales.user_id', $user);
                        })
                        ->when($referenceNumber, function($query, $referenceNumber){
                            return $query->where('sales.reference_number', 'LIKE', '%'.$referenceNumber.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('sales.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon(strval($fromDate));
                            return $query->where('sales.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon(strval($toDate));
                            return $query->where('sales.created_at', '<=', $d);
                        })
                        ->with(['in_payments', 'customer', 'user']);
        $reports = $reportQuery->get();
        $header = [['key'=>'reference_number', 'label'=>'reference_number'], ['key'=> 'created_at', 'label'=> 'date']
                    , ['key'=> 'user', 'label'=> 'sales rep'], ['key'=> 'customer', 'label'=> 'customer']
                    , ['key'=> 'total_cost', 'label'=> 'invoice total']];
        $reportData = ['reports'=> $reports, 'header'=> $header];            
        return $reportData;                    
    }

    public function getSaleReportByProduct($input)
    {
        $invoiceType = $input['type'];
        $isRange = $input['is_range'];
        $reportBy = $input['by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['reference_number'];
        $user = $input['user'];
        $toDate = $input['to_date'];
        $reportQuery = SaleEntry::when($product, function($query, $product){
                            return $query->where('sales.product', 'LIKE', '%'.$product.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('sale_entries.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->where('sale_entries.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->where('sale_entries.created_at', '<=', $d);
                        })
                        ->with(['sale', 'product', 'stock_unit']);
        $reports = $reportQuery->get();
        $header = [['key'=> 'product.label', 'label'=> 'product'], ['key'=>'created_at', 'label'=>'date']
                    ,['key'=> 'quantity', 'label'=> 'quantity']
                    , ['key'=> 'selling_price', 'label'=> 'selling-price'], ['key'=> 'amount', 'label'=> 'amount']];
        $reportData = ['reports'=> $reports, 'header'=>$header];            
        return $reportData;                    
    }

    public function getPurchaseReportByTransaction($input)
    {
        $reports = [];
        $header = [];
        $invoiceType = $input['type'];
        $isRange = $input['is_range'];
        $reportBy = $input['by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['reference_number'];
        $user = $input['user'];
        $toDate = $input['to_date'];
        $reportQuery = Purchase::when($user, function($query, $user, $userInfo){
                            $userInfo = User::findOrFail($user)->name;
                            return $query->where('purchases.user_id', $user);
                        })
                        ->when($referenceNumber, function($query, $referenceNumber){
                            return $query->where('purchases.reference_number', 'LIKE', '%'.$referenceNumber.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('purchases.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon(strval($fromDate));
                            return $query->where('purchases.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon(strval($toDate));
                            return $query->where('purchases.created_at', '<=', $d);
                        })
                        ->with(['out_payments', 'supplier', 'user']);
        $reports = $reportQuery->get();
        $header = [['key'=>'reference_number', 'label'=>'reference_number'], ['key'=> 'created_at', 'label'=> 'date']
                    , ['key'=> 'user', 'label'=> 'sales rep'], ['key'=> 'supplier', 'label'=> 'supplier']
                    , ['key'=> 'invoice_total_cost', 'label'=> 'invoice total cost']];
        $reportData = ['reports'=> $reports, 'header'=> $header];            
        return $reportData;          
    }
    public function getPurchaseReportByProduct($input)
    {
        $invoiceType = $input['type'];
        $isRange = $input['is_range'];
        $reportBy = $input['by'];
        $date = $input['date'];
        $fromDate = $input['from_date'];
        $product = $input['product'];
        $referenceNumber = $input['reference_number'];
        $user = $input['user'];
        $toDate = $input['to_date'];
        $reportQuery = PurchaseEntry::when($product, function($query, $product){
                            return $query->where('purchase_entries.product', 'LIKE', '%'.$product.'%');
                        })
                        ->when($date, function($query, $date){
                            $d = new Carbon($date);
                            return $query->whereDate('purchase_entries.created_at', $d);
                        })
                        ->when($fromDate, function($query, $fromDate){
                            $d = new Carbon($fromDate);
                            return $query->where('purchase_entries.created_at', '>=', $d);
                        })
                        ->when($toDate, function($query, $toDate){
                            $d = new Carbon($toDate);
                            return $query->where('purchase_entries.created_at', '<=', $d);
                        })
                        ->with(['purchase', 'product', 'stock_unit']);
        $reports = $reportQuery->get();
        $header = [['key'=> 'product.label', 'label'=> 'product'], ['key'=>'created_at', 'label'=>'date']
                    ,['key'=> 'quantity', 'label'=> 'quantity']
                    , ['key'=> 'cost_price', 'label'=> 'cost-price'], ['key'=> 'amount', 'label'=> 'amount']];
        $reportData = ['reports'=> $reports, 'header'=>$header];            
        return $reportData;   
    }
}
