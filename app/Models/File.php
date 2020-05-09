<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Role;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'path', 'folder_id'
    ];

    /**
     * genarate uniue path to file
     * @param string $ext
     * @return string $path
     */
    public static function generatePath($ext)
    {
        do {
            $name = Str::random(25);
            $fileName = "{$name}.{$ext}";
            $path = "files/{$name}.{$ext}";
        } while (File::where('path', $path)->exists());
        return $fileName;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'hiden_files');
    }
}
