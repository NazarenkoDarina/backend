<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/test', function () {
        return Auth::user(); //сделать роутер /login
    });

    Route::post('/CountProductInCart', [CartController::class, 'CountProductInCart']
    ); //вызов функции для получения кол-ва товаров в корзине (в шапку)

});

Route::post('/shops', [ShopController::class, 'GetShops']);

Route::post('/discountedProduct', [ProductController::class, 'GetDiscountedProduct']);

Route::post('/productCategoriesForMenu', [ProductCategoryController::class, 'GetCategoriesForMenu']);

Route::post('/productsByShop/{id}/{count}', [ProductController::class, 'GetProductByShop']);

Route::post('/productsBySubcategory/{id}', [ProductController::class, 'GetProductBySubCategory']);

Route::post('/productsByCategory/{idC}', [ProductController::class, 'GetProductsByCategory']);

Route::post('/addProductInCart', [CartController::class, 'AddProductInCart']);

Route::post('/productInfo/{id}', [ProductController::class, 'GetProductInfo']);

Route::post('/searchProducts/{subStr}', [ProductController::class, 'searchProducts']);

Route::post('/addCountProduct', [CartController::class, 'AddCountProduct']); //увелечение количества товара

Route::post('/delCountProduct', [CartController::class, 'DelCountProduct']); //уменьшение количества товара

Route::post('/deleteProductInCart', [CartController::class, 'DeleteProductInCart']); //удалить товар из корзины

Route::post('/deleteAllProductInCart', [CartController::class, 'DeleteAllProductInCart']
); //удалить все товары из корзины

Route::post('/getInfoCart', [CartController::class, 'GetInfoCart']);

Route::post('/sendCode', [AuthController::class, 'sendCode']);

Route::post('/addNameUser', [UserController::class, 'AddNameUser']);

Route::post('/changePhoneUser', [UserController::class, 'ChangePhoneUser']);

Route::get('/login', [AuthController::class, 'Login']);

Route::post('/register', [AuthController::class, 'register']);
