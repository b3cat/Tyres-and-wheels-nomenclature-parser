<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldsRegExp extends Model
{
    protected $fillable = [
        'field_id',
        'reg_exp_mask',
        'priority'
    ];
}
