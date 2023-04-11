<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class ProductCategoryController extends Controller
{
    public function GetCategoriesForMenu()
    {       
        /*return DB::table('product_category')
        ->join('product_subcategory','product_category.id','=','product_subcategory.id_category')
        ->select('product_category.*','product_subcategory.*')
        ->groupBy('product_subcategory.id')
        ->get();*/

        $categories = Category::all();
        foreach ($categories as $value) {
            $value = $value->subCategories;
        }
        return $categories;
    }
}
