<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InPayment extends Model
{
    protected $table = 'in_payments';

    protected $fillable = ['sale_id', 'user_id', 'count'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
