<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Builder;
use Laratrust\Models\LaratrustPermission;

class Permission extends LaratrustPermission
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

//    public function scopeGetAllPermissionToSelect(){
//        $permissions = static::all(['id', 'display_name']);
//        $permissionsForSelect = [];
//        foreach ($permissions as $permission){
//            $permissionsForSelect[$permission->getKey()] = $permission->getDisplayName();
//        }
//        return $permissionsForSelect;
//    }

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

    /**
     * @param string $name
     * @param string $displayName
     * @param string $description
     */
    public function createCrud(string $name, string $displayName, string $description = null)
    {
        $this->create([
            'name'         => str_slug($name) . '--create',
            'display_name' => $displayName . ' - создание',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--read',
            'display_name' => $displayName . ' - чтение',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--update',
            'display_name' => $displayName . ' - обновление',
            'description'  => $description ?? '',
        ]);
        $this->create([
            'name'         => str_slug($name) . '--delete',
            'display_name' => $displayName . ' - удаление',
            'description'  => $description ?? '',
        ]);
    }
}
