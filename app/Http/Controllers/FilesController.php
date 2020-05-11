<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Role, File, Folder};
use App\Http\Requests\{UpdateFileRequest, UpdateAccessRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, Auth};

class FilesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * admin files page
     */
    public function index()
    {
        return view('admin.adminFiles');
    }

    /**
     * create new file
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $rootFolder = Folder::where('name', Folder::ROOT_FOLDER)->first();
        if (File::where('name', $originalName)->where('folder_id', $rootFolder)->exists()) {
            return response()->json([
                'error' => 'file with this name already exist in this folder'
            ]);
        }
        $ext = $file->getClientOriginalExtension();
        $path = File::generatePath($ext);
        $file->storeAs('files', $path);
        $newFile = File::create([
            'path' => $path,
            'name' => $originalName,
            'folder_id' => $rootFolder->id,
        ]);

        return response()->json([
            'data' => 
                ['file' => $newFile]
        ]);
    }

    /**
     * get all files
     * @return json
     */
    public function get()
    {
        $user = Auth::user();
        $roleId = $user->role->id;

        $files = File::whereDoesntHave('roles', function ($query) use ($roleId) {
            $query->where('roles.id', '=', $roleId);
        })->with('roles')->get();
        return response()->json([
            'data' => [
                'files' => $files
            ]
        ]);
    }

    /**
     * @param UpdateFileRequest $request
     * @param File $file
     * @return json
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        $fileName = $request->has('name') ? $request->name: $file->name;
        $folderId = $request->has('folder_id') ? $request->folder_id: $file->folder_id;
        if (
            File::where('name', $fileName)->where('folder_id', $folderId)->exists()
        ) {
            return response()->json([
                'error' => 'file with this name already exists in this folder'
            ]);
        }

        $file->update($request->all());

        return response()->json([
            'data' => 
                ['file' => $file]
        ]);
    }

    /**
     * @param Request $request
     * @param File $file
     * @return json
     */
    public function destroy(Request $request, File $file)
    {
        Storage::delete('files/' . $file->path);
        $file->delete();
        return response()->json([
            'data' => 
                ['success' => true]
        ]);
    }
    
    /**
     * @param UpdateAccessRequest $request
     * @param File $file
     * @return json
     */
    public function updateAccess(UpdateAccessRequest $request, File $file)
    {
        $file->roles()->detach();
        foreach ($request->roles as $roleId) {
            $file->roles()->attach(Role::find($roleId));
        }

        return response()->json([
            'data' => 
                ['success' => true]
        ]);
    }
}