<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = [
        'name', 'parent', 'display_name', 'description'
    ];

    /**
     * Пользователи, которые принадлежат права.
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'permission_user', 'permission_id', 'user_id');
    }
}
