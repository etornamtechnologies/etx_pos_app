<?php


namespace App;
use  Illuminate\Database\Eloquent\Model;
use App\User;

class Board extends Model {


    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}