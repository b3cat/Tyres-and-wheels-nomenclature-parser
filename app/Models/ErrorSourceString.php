<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorSourceString extends Model
{
    protected $fillable = [
      'product_id', 'source_string'
    ];

    public function product(){
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
}
