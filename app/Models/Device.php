<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    public $timestamps = false;
    protected $fillable = [
        'id', 'key', 'name', 'host', 'port', 'update', 'status', 'lastUpdate'
    ];

    public function params()
    {
        return $this->hasMany('App\Models\DeviceParams', 'id_device', 'id')->with('unit');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\DevicesGroup', 'devices_has_group', 'id_device', 'id_group')->withPivot([
            'id_device',
            'id_group'
        ]);
    }

    public function cameras()
    {
        return $this->belongsToMany('App\Models\Camera', 'devices_has_cameras', 'id_device', 'id_camera')->withPivot([
            'id_device',
            'id_camera'
        ]);
    }
}
