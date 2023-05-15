<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Subcategory;
use Elasticsearch;


class ProductController extends Controller
{
    public function GetDiscountedProduct()
    {
        $products_magnit = Product::select(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
          ->where([['discounted_cost', '!=', 'NULL'],['shop_id','=','2']])
          ->inRandomOrder()->limit(10)->get();

        foreach ($products_magnit as $value) {
            $value = $value->Shop;
        }
        $products_perecrestock = Product::select(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
          ->where([['discounted_cost', '!=', 'NULL'],['shop_id','=','4']])
          ->inRandomOrder()->limit(10)->get();
                  
          foreach ($products_perecrestock as $value) {
            $value = $value->Shop;
          }


        return [$products_magnit,$products_perecrestock];
    }

    public function GetProductByShop($id, $count)
    {
        $products = Product::select(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
                           ->where('shop_id', '=', $id)->skip($count - 10)->take($count)->get();

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
        $array      = [];
        foreach ($categories as $value) {
            array_push($array, $value = $value->subCategories);
        }


        // return $array;
        foreach ($array as $value) {
            foreach ($value as $s) {
                $s = $s->Products;
            }
        }

        return $array;
    }

    public function GetProductInfo($id)
    {
        $product = Product::where('id', $id)->get();
        $shopId = $product[0]->shop_id;
        $brand = $product[0]->brand;
        $q = $product[0]->name_product;
        if ($q) {
            $response = Elasticsearch::search([
                'index' => 'products',
                'body'  => [
                    'query' => [
                        'multi_match' => [
                            'type' => 'best_fields',
                            'query' => "('name_product':$q) and ('brand':$brand)",
                            
                        ]
                        
                    ]
                ]
            ]);
            $productsIds = array_column($response['hits']['hits'], '_id');
            $i=0;
            $bestscore='';
            foreach ($productsIds as $id1){
                if($i==0){
                    if(Product::where('id',$id1)->where('shop_id','<>',$shopId)->exists()){
                        $bestscore = Product::where('id',$id1)->where('shop_id','<>',$shopId)->get();
                        $i=1;

                        unset($productsIds[array_search($id1, $productsIds)]);
                    }
                }
            }
        }
        $product[0]->shop;

        foreach ($bestscore as $value) {
            $value->shop;
        }
        return [$product,$bestscore];
    }

    public function searchProducts($subStr)
    {
        $products = Product::like($subStr)->get();

        return $products;
    }
}
