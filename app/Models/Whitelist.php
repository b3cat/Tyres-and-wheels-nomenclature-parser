<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Whitelist extends Model
{
    protected $fillable = [
        'whitelisted_type', 'whitelisted_id', 'string'
    ];

    public function whitelisted(){
        return $this->morphTo();
    }
}
