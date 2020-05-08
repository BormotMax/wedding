<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Role, File, Folder};
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

    public function store(Request $request)
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $ext = $file->getClientOriginalExtension();
        $path = File::generatePath($ext);
        $file->storeAs('files', $path);
        $rootFolder = Folder::where('name', Folder::ROOT_FOLDER)->first();
        $newFile = File::create([
            'path' => $path,
            'name' => $originalName,
            'folder_id' => $rootFolder->id,
        ]);

        return response()->json([
            'data' => $newFile
        ]);
    }
}