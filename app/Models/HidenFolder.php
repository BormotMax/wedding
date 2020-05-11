<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HidenFolder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_id', 'folder_id'
    ];
}
