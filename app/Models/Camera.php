<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    protected $fillable = [
        'id', 'name', 'description', 'stream', 'substream', 'login', 'password'
    ];
    /*protected $hidden = [
        'stream', 'substream'
    ];*/
}
