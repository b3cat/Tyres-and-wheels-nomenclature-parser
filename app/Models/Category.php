<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'category_id', 'category_parent_id', 'name_ru-RU',
    ];
    public function parentCategory()
    {
        return $this->hasOne('App\Models\Category', 'category_id', 'category_parent_id');
    }

}
