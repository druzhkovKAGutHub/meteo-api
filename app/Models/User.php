<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Traits\HasPermissionsTrait;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasPermissionsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'fio', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function devices()
    {
        return $this->belongsToMany(Device::class, 'user_has_devices', 'user_id', 'device_id');
    }

    public function cameras()
    {
        return $this->belongsToMany(Camera::class, 'user_has_cameras', 'user_id', 'camera_id');
    }
}
