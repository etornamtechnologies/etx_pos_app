<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:16 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = "manufacturers";

    protected $fillable = ['label', 'email', 'phone'];

    public function products()
    {
        return $this->hasMany('App\Product');
    }
}