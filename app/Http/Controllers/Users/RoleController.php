<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\Permission;
use App\Models\Users\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use function MongoDB\BSON\toJSON;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    protected $roles;
    protected $permissions;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Role $roles, Permission $permissions)
    {
        $this->roles = $roles;
        $this->permissions = $permissions;
        Route::model('role',Role::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $frd   = $request->all();
        $roles = $this
            ->roles
            ->filter($frd)
            ->orderby('name', 'ASC')
            ->paginate($frd['perPage'] ?? $this->roles->getPerPage())
            ->appends($frd);

        return view('roles.index', compact('roles', 'frd'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
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
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'string|max:255',
            'description' => 'max:255'
        ]);
        $frd = $request->all();
        $role = $this->roles->create($frd);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Роль «' . $role->getDisplayName() . '» успешно создан.',
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
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'display_name' => 'string|max:255',
            'description' => 'string|max:255'
        ]);
        $frd = $request->all();
        $role->update($frd);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Роль «' . $role->getDisplayName() . '» успешно изменен.',
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
     * @param Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->roles->destroy($role->getKey());

        $flashMessage = [
            'type' => 'success',
            'text' => 'Роль успешно удалена.',
        ];
        $response = response()->json($flashMessage);

        return $response;
    }
    public function actionsDestroy(Request $request)
    {
        $frd = $request->only('roles');

        $this->roles->destroy($frd['roles']);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Роли успешно удалены.',
        ];
        $response = response()->json($flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     * @param Role    $role
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function permissions(Request $request, Role $role)
    {
        $frd = $request->all();

        $permissions = $this->permissions
            ->filter($frd)
            ->orderby('name', 'ASC')
            ->paginate($frd['perPage'] ?? $this->roles->getPerPage())
            ->appends($frd);
        return view('roles.permissions', compact('permissions', 'role', 'frd'));
    }

    /**
     * @param Request $request
     * @param Role    $role
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function permissionsUpdate(Request $request, Role $role)
    {

        $frd = $request->only(['permissions']);

        if (isset($frd['permissions']['off']))
        {
            $role->detachPermissions(array_keys($frd['permissions']['off']));
        }

        if (isset($frd['permissions']['on']))
        {
            $role->attachPermissions(array_keys($frd['permissions']['on']));
        }

        $flashMessage = [
            'type' => 'success',
            'text' => 'Разрешения для роли «' . $role->getDisplayName() . '» успешно обновлены',
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
}
