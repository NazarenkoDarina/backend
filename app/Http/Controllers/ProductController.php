<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Shop;


class ProductController extends Controller
{
   public function GetDiscountedProduct()
   {
      $products= Product::select(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
         ->where('discounted_cost', '!=', 'NULL')->inRandomOrder()->limit(20)->get();
         foreach ($products as $value) {
            $value = $value->Shop;
         }
         return $products;
   }

   public function GetProductByShop($id, $count)
   {

      $products = Product::all(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
         ->where('shop_id', '=', $id)->take($count);

      $shop = Shop::select(['id', 'name_shop'])->where('id', '=', $id)->get();

      return [$shop, $products];
   }

   public function GetProductBySubCategory($id)
   {
      $subCategories = Subcategory::select(['id', 'name_subcategory'])
         ->where('id', '=', $id)->get();
      foreach ($subCategories as $value) {
         $value = $value->Products;
      }
      return $subCategories;
   }

   public function GetProductsByCategory($idC)
   {
      $categories = Category::select('*')
      ->where('id', '=', $idC)->get();
      $array=[];
      foreach ($categories as $value) {
         array_push($array,$value = $value->subCategories);
         
      }


      // return $array;
      foreach ($array as $value) {
         foreach ($value as  $s) {
            $s=$s->Products;
         }
      } 
      return $array;
   }

   public function GetProductInfo($id)
   {
      return Product::select('*')->where('id','=',$id)->get();
   }
}
