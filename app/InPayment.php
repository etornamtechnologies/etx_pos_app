<?php

namespace App;

use App\InPayment;
use Illuminate\Database\Eloquent\Model;

class InPayment extends Model
{
    protected $table = 'in_payments';

    protected $fillable = ['sale_id', 'user_id', 'count'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public static function totalPaymentForSale($saleId)
    {
        $total = 0;
        $payments = InPayment::where('sale_id', $saleId)->get();
        foreach($payments as $payment) {
            $total += $payment->count;
        }
        return $total;
    }
}
