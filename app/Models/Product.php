<?php

namespace App\Models;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'source_string',
        'errors',
        'non_critical_errors'
    ];
    public function fields(){
        return $this->hasMany('App\Models\ProductField', 'product_id','id');
    }
    public function model(){
        return $this->hasOne('App\Models\Category', 'category_id', 'category_id');
    }
    static function errorProducts(){
        $productField = new ProductField;
//        dd($productField->where('fields_value_id', 0)->get());

        return Product::whereHas('fields', function ($query) {
            /**
             * @var QueryBuilder $query
             */
            $query->where([
                ['fields_value_id', 0],
                ['field_id', '<>', 9],
            ]);
        })->with(['fields.value', 'fields.field' , 'model.parentCategory'])->paginate(10);
    }
    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeErrors($query)
    {
        return $query->where('errors', true)->with(['fields.value', 'fields.field' , 'model.parentCategory']);
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeNonCriticalErrors($query)
    {
        return $query->where('non_critical_errors', true)->with(['fields.value', 'fields.field' , 'model.parentCategory']);
    }
}
