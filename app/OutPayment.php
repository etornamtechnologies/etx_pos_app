<?php

namespace App;

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
}
