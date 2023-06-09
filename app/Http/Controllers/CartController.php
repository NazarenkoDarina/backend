<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Elasticsearch;


class CartController extends Controller
{
    public function CountProductInCart()
    {
        $count = 0;
        if (Cart::where('customer_id', Auth::user()->id)->exists()) {
            $cartId = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;
            $count  = CartProduct::where("cart_id", $cartId)->count();
        }

        return $count;
    }

    public function AddCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        if (CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->exists()) {
            $count = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
            CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
                'count' => $count + 1.0
            ]);
        } else {
            CartProduct::create([
                'product_id' => $request->id,
                'cart_id'    => $cartId,
                'count'      => 1.0
            ]);
        }
    }

    public function DelCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        if (CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->exists() && 
                                CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count>1){
            $count  = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
            CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
                'count' => $count - 1.0
            ]);
        }else{
            CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->delete();
        }
    }

    public function DeleteProductInCart(Request $request)
    {
        $cartId = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->delete();
    }

    public function DeleteAllProductInCart()
    {
        $cartId = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        CartProduct::where("cart_id", $cartId)->delete();
    }

    public function GetInfoCart()
    {
        $cartId         = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $productsInCart = CartProduct::select('product_id', 'count')->where("cart_id", $cartId)->get();
        $weight         = 0;
        $productsIds    = [];
        foreach ($productsInCart as $product) {
            $weight += Product::where('id', $product->product_id)->get()[0]->weight * $product->count;
            array_push($productsIds, $product->product_id);
        }
        $products = Product::query()->findMany($productsIds);
        $data     = [
            'weight'   => $weight,
            'cart' => $cartId,
            'products' => $products
        ];

        foreach ($data['products'] as  $value) {
            $value->cartProduct;
        }

        return $data;
    }

    public function ComparisonCart()
    {
        $cartId         = Cart::where('customer_id', Auth::user()->id)->get()[0]->id;
        $productsInCart = CartProduct::select('product_id', 'count')->where("cart_id", $cartId)->get();
        $productsIds    = [];
        foreach ($productsInCart as $product) {
            $productsIds += [$product->product_id => $product->count];
        }
        $shopsId   = Shop::select('id', 'name_shop', 'logo_shop')->get();
        $bestscore = [];
        foreach ($shopsId as $shopId) {
            $productInShop   = [];
            $productInShopAnalog  = [];
            $noProductInShop = [];
            $endCount        = 0;
            $endSum          = 0;
            $endWeight       = 0;
            foreach ($productsIds as $prodId => $count) {
                $product = Product::where('id', $prodId)->get();
                $brand   = $product[0]->brand;
                $q       = $product[0]->name_product;
                if ($q) {
                    $response     = Elasticsearch::search([
                        'index' => 'products',
                        'body'  => [
                            'query' => [
                                'multi_match' => [
                                    'query' => "('name_product':$q) and ('brand':$brand) and ('shop_id':$shopId->id)",
                                    'type'  => 'best_fields',
                                ]
                            ]
                        ]
                    ]);
                    $productsIds1 = array_column($response['hits']['hits'],'_score', '_id');
                    $i            = 0;
                    $j            = 0;
                    foreach ($productsIds1 as $prodId1=>$score) {  
                        if ($i == 0) {
                            if (Product::where('id', $prodId1)->where('shop_id', $shopId->id)->exists()) {
                                if($score>20.00){
                                    array_push(
                                        $productInShop,
                                        Product::where('id', $prodId1)->where('shop_id', $shopId->id)->get()[0]
                                        );
                                    }
                                else if($score<20.00) {
                                    array_push(
                                        $productInShopAnalog,
                                        Product::where('id', $prodId1)->where('shop_id', $shopId->id)->get()[0]
                                        );
                                }
                                $endCount += 1;
                                if (Product::where('id', $prodId1)->get()[0]->discounted_cost > 0) {
                                    $endSum += Product::where('id', $prodId1)->get()[0]->discounted_cost * $count;
                                } else {
                                    $endSum += Product::where('id', $prodId1)->get()[0]->cost * $count;
                                }
                                $endWeight += Product::where('id', $prodId1)->get()[0]->weight * $count;
                                $i         = 1;
                            }
                        }                  
                    }
                    if ($i == 0) {
                        array_push($noProductInShop, Product::where('id', $prodId)->get()[0]);
                    }
                }
            }
            array_push($bestscore, [
                    'name_shop'   => $shopId->name_shop,
                    'logo_shop'   => $shopId->logo_shop,
                    'count'       => $endCount,
                    'weight'      => $endWeight,
                    'summ'        => $endSum,
                    'products'    => $productInShop,
                    'analog'      => $productInShopAnalog,
                    'no_products' => $noProductInShop
                ]
            );
        }
        $comparison=[];
        foreach ($bestscore as $mass){
            array_push($comparison,[
                'shop'=>$mass['name_shop'],
                'summ'=>$mass['summ'],
                'count'=>$mass['count']
                ]
            );
        }
        $copmSumm=999999999;
        $copmCount=0;
        foreach($comparison as $comp){
            if($copmSumm>$comp['summ']){
                $copmSumm = $comp['summ'];
            }
            if($copmCount<$comp['count']){
                $copmCount = $comp['count'];
            }
        }
        $endResult=[];
        foreach($bestscore as $mass){
            if($copmCount==$mass['count']){
                if($copmSumm==$mass['summ']){
                    array_unshift($endResult, $mass);
                }
                else{
                    array_push($endResult, $mass);
                }
            }else{
                array_push($endResult, $mass);
            }
        }
        return $endResult;
    }
}
