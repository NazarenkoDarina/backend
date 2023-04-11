<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;


class ProductController extends Controller
{
   public function GetDiscountedProduct()
   {
        return Product::select(['id','name_product','image','id_shop','cost','discounted_cost'])
        ->where('discounted_cost','!=','NULL')->inRandomOrder()->limit(20)->get();
   }
}
