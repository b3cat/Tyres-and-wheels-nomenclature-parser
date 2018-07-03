<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomenclature extends Model
{
    protected $fillable = [
        'source_string', 'product_manufacturer'
    ];

    public function isTire(){
        $categoryModel = new Category;
        $category = $categoryModel->where('category_id', '=', $this->{'product_manufacturer'})->first();
        return ($category->{'category_parent_id'} === 1) ? true : false;
    }
}
