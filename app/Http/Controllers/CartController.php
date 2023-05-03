<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartProduct;

class CartController extends Controller
{
    public function AddProductInCart(Request $request)
    {
        if (Cart::where('customer_id', "2")->exists()) { //"2" - Auth::User()->id; Тест-данные
            $cartId = Cart::where('customer_id', "2")->get()[0]->id; //"2" - Auth::User()->id; Тест-данные
            if (CartProduct::where('product_id', $request->id)->exists()) {
                CartProduct::where('product_id', $request->id)->update([
                    'count' => $request->count
                ]);
            } else {
                CartProduct::create([
                    'product_id' => $request->id,
                    'cart_id'    => $cartId,
                    'count'      => $request->count
                ]);
            }
        } else {
            $cartId = Cart::create([
                'customer_id' => "2"  //"2" - Auth::User()->id; Тест-данные
            ])->id;
            CartProduct::create([
                'product_id' => $request->id,
                'cart_id'    => $cartId,
                'count'      => $request->count
            ]);
        }
    }
    public function CountProductInCart()
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $count = CartProduct::where("cart_id", $cartId)->count();
        return $count;
    }
    public function AddCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $count = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
            'count'=> $count+1.0
        ]);
    }
    public function DelCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $count = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
            'count'=> $count-1.0
        ]);
    }
    public function DeleteProductInCart(Request $request)
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->delete();
    }
    public function DeleteAllProductInCart()
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        CartProduct::where("cart_id", $cartId)->delete();
    }
}
