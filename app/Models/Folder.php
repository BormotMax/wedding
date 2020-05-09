<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{

    const ROOT_FOLDER = 'root';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'parent_folder_id'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'hiden_folders', 'folder_id', 'role_id');
    }
}
