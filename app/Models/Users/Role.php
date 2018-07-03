<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Builder $query
     * @param array   $frd
     *
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $frd): Builder
    {
        $fillable = $this->fillable;
        foreach ($frd as $key => $value)
        {
            if ($value === null)
            {
                continue;
            }
            switch ($key)
            {
                case 'search':
                    {
                        $query->where(function ($query) use ($value) {
                            $query->orWhere('name', 'like', '%' . $value . '%')
                                ->orWhere('display_name', 'like', '%' . $value . '%');
                        });
                    }
                    break;
                default:
                    {
                        if (in_array($key, $fillable))
                        {
                            $query->where($key, $value);
                        }
                    }
                    break;
            }
        }

        return $query;
    }
}
