<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable=['favorite_list', 'customer_id', 'name'];
    public $timestamps=false;

}
