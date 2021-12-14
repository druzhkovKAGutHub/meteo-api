<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DevicesGroup extends Model
{
    protected $table = 'devices_group';
    protected $fillable = [
        'name', 'parent_id'
    ];

    public function devices()
    {
        return $this->belongsToMany('App\Models\Device', 'devices_has_group', 'id_device', 'id_group');
    }
}
