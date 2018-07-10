<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldsValue extends Model
{
    protected $fillable = [
      'fields_value_id', 'field_id', 'name_ru-RU'
    ];
    public function field(){
        return $this->belongsTo('App\Models\Field', 'field_id', 'field_id');
    }
}
