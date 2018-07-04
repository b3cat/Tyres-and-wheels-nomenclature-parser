<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;
    public $asYouType = true;
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...

        return $array;
    }
    /**
     * @var array
     */
    protected $fillable = [
        'category_id', 'category_parent_id', 'name_ru-RU',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parentCategory()
    {
        return $this->hasOne('App\Models\Category', 'category_id', 'category_parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function whitelist(){
        return $this->morphMany('App\Models\WhiteList', 'whitelisted');
    }
    public function isTire(){
        return $this->{'category_parent_id'} === 1 ? true : false;
    }
    public function isModel(){
        return ($this->{'category_parent_id'} !== 1 && $this->{'category_parent_id'} !== 2)? true : false;
    }
    public function whitelistRegExp(){
        $whitelistItems = $this->{'whitelist'};
        $whitelistRegExp = preg_quote($this->{'name_ru-RU'}, '~');
        foreach ($whitelistItems as $item){
            $whitelistRegExp .= '|'.preg_quote($item->{'string'});
        }
        $whitelistRegExp = '~(?<category>'.$whitelistRegExp.')~i';
        return $whitelistRegExp;
    }

}
