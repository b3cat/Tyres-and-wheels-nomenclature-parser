<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductField extends Model
{
    protected $fillable = [
        'product_id', 'field_id', 'fields_value_id'
    ];
    public function field(){
        return $this->belongsTo(Field::class, 'field_id', 'field_id');
    }
    public function value(){
        return $this->belongsTo(FieldsValue::class, 'fields_value_id', 'fields_value_id');
    }
}
