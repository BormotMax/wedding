<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_PHOTOGRAPHER = 'photographer';
    const ROLE_MODERATOR = 'moderator';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_name',
    ];

    public $timestamps = false;
}
