<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $fillable=['product_id', 'cart_id', 'count'];
    public $timestamps=false;
}
