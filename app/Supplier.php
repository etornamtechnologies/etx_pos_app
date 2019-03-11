<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 29-Jan-19
 * Time: 6:10 AM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Purchase;

class Supplier extends Model
{
    protected $table = "suppliers";

    protected $fillable = ["name", "email", "address", "phone"];

    public function sales()
    {
        return $this->hasMany("App\Purchase");
    }

    public function purchases()
    {
        return $this->hasMany('App\purchases', 'purchase_id');
    }
}