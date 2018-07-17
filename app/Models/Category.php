<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;
    public $asYouType = true;


    public function fields()
    {
        return $this->hasMany('App\Models\Field', 'group', 'category_id');
    }
    public function fieldsWithPairs(){
        $fields = $this->{'fields'};
        $allFields = collect([]);
        $toForget = [];
        foreach ($fields as $field){
            /**
             * @var Field $field
             */
            $allFields->put($field->{'field_id'}, $field);
            if($field->isPairField()){
                if(!isset($toForget[$field->{'field_id'}])){
                    $toForget[$field->{'pairField'}->{'field_id'}] = true;
                }
            }
        }
        foreach ($toForget as $key => $item){
            $allFields->forget($key);
        }
        return $allFields;
    }
    public function pairFields(){
        $fields = $this->{'fields'};
        $pairFields = collect([]);
        $toForget = [];
        foreach ($fields as $field){
            /**
             * @var Field $field
             */
            if($field->isPairField()){
                $pairFields->put($field->{'field_id'}, $field);
                if(!isset($toForget[$field->{'field_id'}])){
                    $toForget[$field->{'pairField'}->{'field_id'}] = true;
                }
            }
        }
        foreach ($toForget as $key => $item){
            $pairFields->forget($key);
        }
        return $pairFields;
    }

    public function isMainCategory()
    {
        return is_null($this->{'category_parent_id'});
    }
    public function mainCategories(){
        return $this->where('category_parent_id', null)->with('fields.regExpMasks')
            ->get()->sortBy('category_id');
    }

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
        'category_id', 'category_parent_id', 'name_ru-RU', 'short_description_ru-RU',
    ];

    protected $casts = [
        'short_description_ru-RU' => 'object'
    ];


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('alphabet', function (Builder $builder) {
            $builder->orderBy('name_ru-RU');
        });
    }
    public function regExp(){
        return $this->hasOne(CategoriesRegExp::class, 'category_id', 'category_id');
    }
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
    public function whitelist()
    {
        return $this->morphMany('App\Models\WhiteList', 'whitelisted');
    }

    public function isTire()
    {
        return $this->{'category_parent_id'} === 1 ? true : false;
    }

    public function isModel()
    {
        return ($this->{'category_parent_id'} !== 1 && $this->{'category_parent_id'} !== 2) ? true : false;
    }

    public function whitelistRegExp()
    {
        $whitelistItems = $this->{'whitelist'};
        $whitelistRegExp = preg_quote($this->{'name_ru-RU'}, '~');
        foreach ($whitelistItems as $item) {
            $whitelistRegExp .= '|' . preg_quote($item->{'string'});
        }
        $whitelistRegExp = '~(?<category>' . $whitelistRegExp . ')~i';
        return $whitelistRegExp;
    }

    static function whitelists($parentCategory = null)
    {
        $categories = Category::withoutGlobalScope('alphabet')->with('whitelist')
            ->where('category_parent_id', $parentCategory)
            ->get();
        $whitelists = [];
        foreach ($categories as $category) {
            $whitelistBody = [
                $category->{'name_ru-RU'}
            ];
            foreach ($category{'whitelist'} as $whitelist) {
                $whitelistBody[] = $whitelist->{'string'};
            }
            $whitelists[$category->{'category_id'}] = $whitelistBody;
        }
        $whitelists = collect($whitelists)->sortBy(function ($item) {
            return mb_strlen($item[0]);
        });

        return $whitelists;
    }

}
