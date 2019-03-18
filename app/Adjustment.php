<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\User;
use App\AdjustmentEntry;

class Adjustment extends Model
{
    protected $table = "adjustments";

    protected $fillable = ["ref_code", "reason_id", "user_id"];

    public function entries()
    {
        return $this->hasMany("App\AdjustmentEntry");
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function reason()
    {
        return $this->belongsTo('App\Reason', 'reason_id');
    }
}