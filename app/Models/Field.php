<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Field extends Model
{
    protected $fillable = [
        'field_id', 'group', 'name_ru-RU',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(FieldsValue::class, 'field_id', 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pairField()
    {
        return $this->hasOne(FieldsValue::class, 'field_id', 'pair_field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regExpMasks()
    {
        return $this->hasMany(FieldsRegExp::class, 'field_id', 'field_id');
    }

    /**
     * @return integer
     */
    public function getFieldId()
    {
        return $this->{'field_id'};
    }

    /**
     * @return integer
     */
    public function getGroup()
    {
        return $this->{'group'};
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->{'name_ru-RU'};
    }

    /**
     * @return bool
     */
    public function isPairField()
    {
        if (!is_null($this->{'pairField'})) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Collection
     */
    public function valuesList()
    {
        $values = $this->{'values'};
        $response = ($values->pluck('name_ru-RU', 'fields_value_id'));
        $response->prepend('Нет значения', 0);
        return $response;
    }

    /**
     * @return array
     */
    static function allValueLists()
    {
        $fields = Field::all();
        $response = [];
        foreach ($fields as $field) {
            $response[$field->{'field_id'}] = $field->valuesList();
        }
        return collect($response);
    }

    /**
     * @return string
     */
    public function prepareForRegExp()
    {
        $values = $this->{'values'};
        $values = $values->sortByDesc(function ($value) {
            return strlen($value->{'name_ru-RU'});
        });

        $preparedString = '';
        foreach ($values as $key => $value) {
            $preparedString .= '|' . preg_quote($value->{'name_ru-RU'}, '~');
        }
        $preparedString = preg_replace('~\|~', '', $preparedString, 1);
        $preparedString = '(?<field' . $this->{'field_id'} . '>' . $preparedString . ')';

        return $preparedString;
    }
}
