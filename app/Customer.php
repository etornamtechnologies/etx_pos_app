<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:10 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Sale;

class Customer extends Model
{
    protected $table = "customers";

    protected $fillable = ["name", "email", "address", "phone"];

    public function sales()
    {
        return $this->hasMany("App\Sale");
    }
}