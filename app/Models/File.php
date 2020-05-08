<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
            $path = "files/{$name}.{$ext}";
        } while (File::where('path', $path)->exists());
        return $path;
    }
}
