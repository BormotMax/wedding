<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{Role};
use Illuminate\Http\Request;

class RolesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * get all roles
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json([
            'data' => [
                'roles' => $roles
            ]
        ]);
    }
}