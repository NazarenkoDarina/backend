<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $count = 0;
        if (Cart::where('customer_id', Auth::user()->id)->exists()) {
            $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
            $count  = CartProduct::where("cart_id", $cartId)->count();
        }

        return $count;
    }

    public function AddCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $count  = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
            'count' => $count + 1.0
        ]);
    }

    public function DelCountProduct(Request $request)
    {
        $cartId = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
        $count  = CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->get()[0]->count;
        CartProduct::where("cart_id", $cartId)->where("product_id", $request->id)->update([
            'count' => $count - 1.0
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

    public function GetInfoCart()
    {
        $cartId         = Cart::where('customer_id', "2")->get()[0]->id;//"2" - Auth::User()->id; Тест-данные
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
            'products' => $products
        ];

        return $data;
    }
}
