<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Role};
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class FilesManagementController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * admin files page
     */
    public function index()
    {
        return view('admin.adminFiles');
    }

}