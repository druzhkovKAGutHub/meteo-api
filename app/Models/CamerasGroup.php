<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CamerasGroup extends Model
{
    protected $table = 'cameras_groups';
    protected $fillable = [
        'name'
    ];

    public function cameras()
    {
        return $this->belongsToMany(Camera::class, 'cameras_has_groups', 'group_id', 'camera_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cameras_group_has_users', 'group_id', 'user_id');
    }
}
