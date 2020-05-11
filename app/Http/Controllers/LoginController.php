<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class LoginController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * login page
     * @return view
     */
    public function index()
    {
        return view('login');
    }

    /**
     * make sing in
     * redirect if success
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('key', $request->key)->first();

        if (! $user || ! Auth::loginUsingId($user->id)) {
            return view('login', ['error' => 'login failed']);
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.main');
        }

        return redirect()->route('user.cabinet');
    }

    /**
     * Sign out
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login.page');
    }
}