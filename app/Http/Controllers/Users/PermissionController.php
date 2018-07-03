<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    protected $permissions;

    /**
     * Create a new controller instance.
     *
     * @param Permission $permissions
     */
    public function __construct(Permission $permissions)
    {
        $this->permissions = $permissions;
        Route::model('permission',Permission::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $frd   = $request->all();
        $permissions = $this
            ->permissions
            ->filter($frd)
            ->orderby('name', 'ASC')
            ->paginate($frd['perPage'] ?? $this->permissions->getPerPage())
            ->appends($frd);

        return view('permissions.index', compact('permissions', 'frd'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:permissions',
            'display_name' => 'string|max:255',
            'description' => 'max:255'
        ]);
        $frd = $request->all();
        if(isset($frd['crud'])){
            (new Permission)->createCrud($frd['name'], $frd['display_name'], $frd['description']);
            $name = $frd['display_name']." - CRUD";
        }else{
            $permission = $this->permissions->create($frd);
            $name = $permission->getDisplayName();
        }

        $flashMessage = [
            'type' => 'success',
            'text' => 'Разрешение «' . $name . '» успешно создано.',
        ];
        if ($request->ajax())
        {
            $response = response()->json($flashMessage);
        }
        else
        {
            $response = redirect()->back()->with(['flash_message' => $flashMessage]);
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'display_name' => 'string|max:255',
            'description' => 'string|max:255'
        ]);
        $frd = $request->all();
        $permission->update($frd);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Разрешение «' . $permission->getDisplayName() . '» успешно создано.',
        ];

        if ($request->ajax())
        {
            $response = response()->json($flashMessage);
        }
        else
        {
            $response = redirect()->back()->with(['flash_message' => $flashMessage]);
        }

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $this->permissions->destroy($permission->getKey());

        $flashMessage = [
            'type' => 'success',
            'text' => 'Разрешение успешно удален.',
        ];
        $response = response()->json($flashMessage);

        return $response;
    }
    public function actionsDestroy(Request $request)
    {
        $frd = $request->only('permissions');

        $this->permissions->destroy($frd['permissions']);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Разрешения успешно удалены.',
        ];
        $response = response()->json($flashMessage);

        return $response;
    }
}
