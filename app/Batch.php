<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $table = "batches";

    protected $fillable = ["product_id", "quantity", "label", "expiry_date"];


}