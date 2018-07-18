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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany(ProductField::class, 'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function model()
    {
        return $this->hasOne(Category::class, 'category_id', 'category_id');
    }

    /**
     * @return string
     */
    public function getSourceString(){
        return $this->{'source_string'};
    }

    /**
     * @return integer
     */
    public function getCategoryId(){
        return $this->{'category_id'};
    }
    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeErrors($query)
    {
        return $query->where('errors', true)->with(['fields.value', 'fields.field', 'model.parentCategory']);
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    public function scopeNonCriticalErrors($query)
    {
        return $query->where('non_critical_errors', true)->with(['fields.value', 'fields.field', 'model.parentCategory']);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    static function errorProducts()
    {
        return Product::whereHas('fields', function ($query) {
            /**
             * @var QueryBuilder $query
             */
            $query->where([
                ['fields_value_id', 0],
                ['field_id', '<>', 9],
            ]);
        })->with(['fields.value', 'fields.field', 'model.parentCategory'])->paginate(10);
    }


}
