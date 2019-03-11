<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Adjustment;

class AdjustmentEntry extends Model
{
    protected $table = "adjustment_entries";

    protected $fillable = ["product_id", "stock_unit_id", "adjustment_id", "old_quantity", "new_quantity"];

    public function adjsutment()
    {
        return $this->belongsTo("App\Adjustment", "adjustment_id");
    }
}