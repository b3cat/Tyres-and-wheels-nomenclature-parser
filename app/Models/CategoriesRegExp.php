<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriesRegExp extends Model
{
    protected $fillable = [
      'category_id',
      'reg_exp_id'
    ];

    public function regExpValue(){
        return $this->hasOne(RegExp::class, 'id', 'reg_exp_id');
    }
}


