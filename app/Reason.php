<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $table = 'adjustment_reasons';

    protected $fillable = ['label'];

    public function adjustments()
    {
        return $this->hasMany('App\Adjustment');
    }
}
