<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use PHPUnit\Util\Json;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $users;
    protected $roles;

    public function __construct(User $users, Role $roles)
    {
        $this->users  = $users;
        $this->roles  = $roles;
        Route::model('user',User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {

        $frd   = $request->all();
        $users = $this
            ->users
            ->filter($frd)
            ->orderby('f_name', 'ASC')
            ->paginate($frd['perPage'] ?? $this->users->getPerPage())
            ->appends($frd);
//        $users = $this->users->paginate(10);
        return view('users.index', compact('users', 'frd'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
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
            'f_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'sex' => 'required|string|max:10',
            'password' => 'required|string|min:6|confirmed',
            'l_name' => 'max:255',
            'm_name' => 'max:255',
        ]);
        $frd = $request->all();
        $user = $this->users->create($frd);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Профиль пользователя «' . $user->f_name . '» успешно создан.',
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
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'f_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'sex' => 'required|string|max:10',
            'l_name' => 'max:255',
            'm_name' => 'max:255',
        ]);
        $frd = $request->all();
        $user->update($frd);

        $flashMessage = [
            'type' => 'success',
            'text' => 'Профиль пользователя «' . $user->f_name . '» успешно обновлен',
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
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $this->users->destroy($user->getKey());

        $flashMessage = [
            'type' => 'success',
            'text' => 'Профиль пользователя успешно удален.',
        ];
        $response = response()->json($flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function roles(Request $request, User $user)
    {
        $frd = $request->all();

        $roles = $this->roles
            ->filter($frd ?? [])
            ->orderby('name', 'ASC')
            ->paginate($frd['perPage'] ?? $this->roles->getPerPage())
            ->appends($frd);

        return view('users.roles', compact('roles', 'user', 'frd'));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function rolesUpdate(Request $request, User $user)
    {
        $frd = $request->only(['roles']);

        if (isset($frd['roles']['off']))
        {
            $user->detachRoles(array_keys($frd['roles']['off']));
        }

        if (isset($frd['roles']['on']))
        {
            $user->attachRoles(array_keys($frd['roles']['on']));
        }

        $flashMessage = [
            'type' => 'success',
            'text' => 'Роли для пользователя «' . $user->getName() . '» успешно обновлены',
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
