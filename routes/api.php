<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/shops', [ShopController::class,'GetShops']);
Route::post('/discountedProduct',[ProductController::class,'GetDiscountedProduct']);

Route::post('/productCategoriesForMenu',[ProductCategoryController::class,'GetCategoriesForMenu']);

Route::post('/productsByShop/{id}/{count}',[ProductController::class,'GetProductByShop']);

Route::post('/productsBySubcategory/{id}',[ProductController::class,'GetProductBySubCategory']);

Route::post('/productsByCategory/{idC}',[ProductController::class,'GetProductsByCategory']);

Route::post('/addProductInCart',[CartController::class,'AddProductInCart']);

Route::post('/productsInfo/{id}',[ProductController::class,'GetProductInfo']);

Route::post('/searchProducts/{subStr}',[ProductController::class,'searchProducts']);