<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_Subcategory extends Model
{
    protected $table = 'subcategories';
    use HasFactory;

    public function Categories()
    {
        return $this->belongsTo(Category::class);
    }
}
