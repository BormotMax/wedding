<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'key', 'role_id'
    ];

    /**
     * @return belongsTo Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return bool $is_admin
     */
    public function isAdmin()
    {
        return $this->role->role_name === Role::ROLE_ADMIN;
    }

    /**
     * create and return new User
     * @return User $user
     */
    public static function createWithKey($data): User
    {
        return User::create([
            'name' => $data['name'],
            'role_id' => $data['role_id'],
            'key' => User::generateKey(),
        ]);
    }

    /**
     * genarate unique user key
     * @return string $key
     */
    public static function generateKey(): string
    {
        do {
            $key = Str::random(10);
        } while (User::where('key', $key)->exists());
        return $key;
    }
}
