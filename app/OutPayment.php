<?php

namespace App;

use App\InPayment;
use App\OutPayment;
use Illuminate\Database\Eloquent\Model;

class OutPayment extends Model
{
    protected $table = 'out_payments';

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo('App\Purchase', 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function getPaymentsForPurchase($purchaseId)
    {
        $payments = OutPayment::where('purchase_id', $purchaseId)->with(['user'])->get();
        return $payments;
    }

    public static function totalPaymentForPurchase($purchaseId)
    {
        $total = 0;
        $payments = OutPayment::where('purchase_id', $purchaseId)->get();
        foreach($payments as $payment) {
            $total += $payment->count;
        }
        return $total;
    }
}
