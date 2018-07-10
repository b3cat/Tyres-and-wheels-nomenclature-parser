<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'field_id', 'group', 'name_ru-RU',
    ];
    public function values(){
        return $this->hasMany('App\Models\FieldsValue', 'field_id', 'field_id');
    }
    public function valuesList(){
        $values = $this->{'values'};
        $response = [];
        $response[0] = 'Нет значения';
        foreach ($values as $value){
            $response[$value->{'fields_value_id'}] = $value->{'name_ru-RU'};
        }
        return $response;
    }
    static function allValueLists(){
        $fields = Field::all();
        $response = [];
        foreach ($fields as $field){
            $response[$field->{'field_id'}] = $field->valuesList();
        }
        return $response;
    }
    public function prepareForRegExp(){
        $values = $this->{'values'};
        $values = $values->sortByDesc(function($value) {
            return strlen($value->{'name_ru-RU'});
        });

        $preparedString = '';
        foreach ($values as $key => $value) {
            $preparedString .= '|'.preg_quote($value->{'name_ru-RU'}, '~');
        }
        $preparedString = preg_replace( '~\|~', '', $preparedString, 1);
        $preparedString = '(?<field'.$this->{'field_id'}.'>'.$preparedString.')';
        return $preparedString;
    }
}
