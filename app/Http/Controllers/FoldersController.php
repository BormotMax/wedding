<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\{User, Role, File, Folder};
use App\Http\Requests\{StoreFolderRequest, UpdateFolderRequest, UpdateAccessRequest};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FoldersController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * create new folder
     * @param StoreFolderRequest $request
     * @return json
     */
    public function store(StoreFolderRequest $request)
    {
        if (Folder::where('name', $request->name)->where('parent_folder_id')->exists()) {
            return response()->json([
                'data' => [
                    'success' => false,
                    'error' => 'such folder already exists'
                ]
            ]);
        }
        $folder = Folder::create($request->all());
        return response()->json([
            'data' => [
                'success' => true,
                'folder' => $folder
            ]
        ]);
    }

    /**
     * get all folders
     */
    public function get()
    {
        $user = Auth::user();
        $roleId = $user->role->id;

        $folders = Folder::whereDoesntHave('roles', function ($query) use ($roleId) {
            $query->where('roles.id', '=', $roleId);
        })->with('roles')->get();

        return response()->json([
            'data' => [
                'folders' => $folders
            ]
        ]);
    }

    /**
     * updata folder
     * @param UpdateFolderRequest $request
     * @param Folder $folder
     * @return json
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $parentId = $request->has('parent_folder_id') ? $request->parent_folder_id: $folder->parent_folder_id;
        $name = $request->has('name') ? $request->name: $folder->name;
        if (
            Folder::where('name', $name)->where('parent_folder_id', $parentId)->exists()
        ) {
            return response()->json([
                'error' => 'folder with this name already exists in this folder'
            ]);
        }
        $folder->update($request->all());
        return response()->json([
            'data' => 
                ['folder' => $folder]
        ]);
    }

    /**
     * destroy folder
     * @param Request $request
     * @param Folder $folder
     * @return json
     */
    public function destroy(Request $request, Folder $folder)
    {
        $this->deleteFolder($folder);
        return response()->json([
            'data' => 
                ['success' => true]
        ]);
    }

    /**
     * delete folder, subfiles and subfolders
     * @param Folder $folder
     */
    private function deleteFolder(Folder $folder)
    {
        $files = File::where('folder_id', $folder->id);
        foreach ($files->get() as $file) {
            Storage::delete('files/' . $file->path);
        }
        $files->delete();

        $subFolders = Folder::where('parent_id', $folder->id);
        foreach ($subFolders as $subFolder) {
            $this->deleteFolder($subFolder);
        }

        $folder->delete();
    }

    /**
     * update access to folder
     * @param UpdateAccessRequest $request
     * @param Folder $folder
     */
    public function updateAccess(UpdateAccessRequest $request, Folder $folder)
    {
        $folder->roles()->detach();
        foreach ($request->roles as $roleId) {
            $folder->roles()->attach(Role::find($roleId));
        }

        return response()->json([
            'data' => 
                ['success' => true]
        ]);
    }
}