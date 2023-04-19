<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class ShopController extends Controller
{
    public function GetShops()
    {
        return Shop::select(['id','name_shop'. 'shop_logo'])->get();
    }
}
