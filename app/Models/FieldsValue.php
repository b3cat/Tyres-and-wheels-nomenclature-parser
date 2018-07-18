<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldsValue extends Model
{
    protected $fillable = [
      'fields_value_id', 'field_id', 'name_ru-RU'
    ];
    public function field(){
        return $this->belongsTo(Field::class, 'field_id', 'field_id');
    }

    public function getName(){
        return $this->{'name_ru-RU'};
    }
    public function getValueId(){
        return $this->{'fields_value_id'};
    }
}
