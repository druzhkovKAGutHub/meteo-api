<?php

namespace App\Events;

use App\Models\Device;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DeviceEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $device_id;

    public function __construct($id)
    {
        $this->device_id = $id;
    }

    public function broadcastAs()
    {
        return 'DeviceEvent';
    }

    public function broadcastOn()
    {
        return new PrivateChannel('device.'.$this->device_id);
    }

    public function broadcastWith()
    {
        $device = Device::where('id', $this->device_id)->with('params','groups')->first();
        /*$device = DB::select('select * from devices where id = :id', ['id' => $this->device_id]);
        foreach ($device as $d) {
            $params = DB::select('select id, name, label, color, classIcon, id_unit from devices_params where id_device = :id', ['id' => $this->device_id]);
            $paramsRez = [];
            foreach ($params as $param) {
                $unit = DB::select('SELECT id, `short`, `name` FROM `units` WHERE `id` = :id', ['id' => $param->id_unit]);
                $param->unit = $unit;
                $table_name = "ParamsData" . $param->id;
                if (Schema::hasTable($table_name)) {
                    $val = DB::select('SELECT value, time FROM ' . $table_name . ' ORDER BY id DESC LIMIT 1');
                    //var_dump($val);die;
                    $param->value = $val[0]->value;
                    $param->time = $val[0]->time;
                } else {
                    $param->value = null;
                    $param->time = null;
                }
                $paramsRez[$param->id] = $param;
            }
            $d->params = $paramsRez;
        }*/

        return [json_encode($device)];
    }
}
