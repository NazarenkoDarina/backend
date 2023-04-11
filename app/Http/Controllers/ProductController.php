<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop;


class ProductController extends Controller
{
   public function GetDiscountedProduct()
   {
        return Product::select(['id','name_product','image','shop_id','cost','discounted_cost'])
        ->where('discounted_cost','!=','NULL')->inRandomOrder()->limit(20)->get();
   }

   public function GetProductByShop($id, $count)
   {

      $products= Product::all(['id','name_product','image','shop_id','cost','discounted_cost'])
      ->where('shop_id','=',$id)->take($count);

      $shop= Shop::select(['id','name_shop'])->where('id','=',$id)->get();

      return [$shop,$products];

   }
}
