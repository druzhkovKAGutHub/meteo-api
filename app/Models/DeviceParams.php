<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceParams extends Model
{
    protected $table = 'devices_params';
    public $timestamps = false;
    protected $fillable = [
        'id_device', 'name', 'label', 'value', 'color', 'id_unit', 'classIcon', 'isHidden', 'isFavorit'
    ];
    
    public function unit()
    {
        return $this->hasOne('App\Models\Unit', 'id', 'id_unit');
    }
}
