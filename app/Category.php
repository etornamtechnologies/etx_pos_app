<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ernest Anyidoho
 * Date: 28-Jan-19
 * Time: 11:15 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Product;

class Category extends Model
{
    protected $table = "categories";

    protected $fillable = ['label'];

    public function products()
    {
        return $this->hasMany('App\Product');
    }
}