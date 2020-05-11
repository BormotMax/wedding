<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Role};
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class UsersManagementController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * admin users page
     * @return view
     */
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('admin.adminUsers', ['users' => $users, 'roles' => $roles]);
    }

    /**
     * store new user
     * @param StoreUserRequest $request
     */
    public function store(StoreUserRequest $request)
    {
        User::createWithKey($request->all());
        return redirect()->route('users.index');
    }

    /**
     * delete user
     * @param Request $request
     * @param User $user
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    /**
     * edit user page
     * @param Request $request
     * @param User $user
     * @return view
     */
    public function edit(Request $request, User $user)
    {
        $roles = Role::all();
        return view('admin.adminUserEdit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * update user
     * @param StoreUserRequest $request
     * @param User $user
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('users.index');
    }
}