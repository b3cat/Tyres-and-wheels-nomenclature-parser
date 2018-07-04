<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $fillable = [
        'name',
        'parsed_items_number',
        'parsed_fields_number',
        'fields_number'
    ];
}
