<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacture extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'name_ru-RU',
    ];

}
