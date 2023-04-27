<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Subcategory;


class ProductController extends Controller
{
    public function GetDiscountedProduct()
    {
        $products = Product::select(['id', 'name_product', 'image', 'shop_id', 'cost', 'discounted_cost'])
                           ->where('discounted_cost', '!=', 'NULL')->inRandomOrder()->limit(20)->get();
        foreach ($products as $value) {
            $value = $value->Shop;
        }

        return $products;
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
        $product     = Product::where('id', $id)->get();
        $nameProduct = explode(', ', $product[0]->name_product);
        $sameProduct = Product::where('shop_id', '<>', $product[0]->shop_id)->where(
            'name_product',
            'like',
            $nameProduct[0]
        )->get();

        return [$product, $sameProduct];
    }

    public function searchProducts($subStr)
    {
        $products = Product::like($subStr)->limit(10)->get();

        return $products;
    }
}
