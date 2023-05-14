<?php

namespace App\Models;

use App\Services\LinguaStemRu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function Shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function Subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function cartProduct()
    {
        return $this->hasMany(CartProduct::class);
    }

    public function scopeLike($query, $s)
    {
        $s = iconv_substr($s, 0, 64);
        $s = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $s);
        $s = preg_replace('#\s+#u', ' ', $s);
        $s = trim($s);

        if (empty($s)) {
            return $query->whereNull('id'); // возвращаем пустой результат
        }
        $temp    = explode(' ', $s);
        $words   = [];
        $stemmer = new LinguaStemRu();
        foreach ($temp as $item) {
            if (iconv_strlen($item) > 3) {
                $words[] = $stemmer->stem_word($item);
            } else {
                $words[] = $item;
            }
        }
        $relevance = "IF (`products`.`name_product` LIKE '%" . $words[0] . "%', 2, 0)";
        for ($i = 1; $i < count($words); $i++) {
            $relevance .= " + IF (`products`.`name_product` LIKE '%" . $words[$i] . "%', 2, 0)";
        }
        $query->select('products.*', Product::raw($relevance . ' as relevance'))
              ->where('products.name_product', 'like', '%' . $words[0] . '%');
        for ($i = 1; $i < count($words); $i++) {
            $query = $query->orWhere('products.name_product', 'like', '%' . $words[$i] . '%');
        }
        $query->orderBy('relevance', 'desc');

        return $query;
    }
}
