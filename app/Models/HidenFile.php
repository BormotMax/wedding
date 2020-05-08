<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HidenFile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_id', 'role_id'
    ];
}
