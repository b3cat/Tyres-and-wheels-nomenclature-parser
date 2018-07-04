<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Whitelist extends Model
{
    use Searchable;
    public $asYouType = true;
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }
    protected $fillable = [
        'whitelisted_type', 'whitelisted_id', 'string'
    ];

    public function whitelisted(){
        return $this->morphTo();
    }
}
