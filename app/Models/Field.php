<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'field_id', 'field_parent_id', 'name_ru-RU',
    ];
}
