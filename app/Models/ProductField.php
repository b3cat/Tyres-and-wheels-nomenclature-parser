<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductField extends Model
{
    protected $fillable = [
        'product_id', 'field_id', 'fields_value_id'
    ];
}
