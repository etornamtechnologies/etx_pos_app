<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceTemplate extends Model
{
    protected $table = 'price_templates';

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}
