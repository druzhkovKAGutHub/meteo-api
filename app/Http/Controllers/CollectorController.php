<?php

namespace App\Http\Controllers;

use App\Events\DeviceEvent;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CollectorController extends Controller
{
    public function add(Request $request)
    {
        //Log::info($request);die;
        $data = json_decode($request->getContent(), true);
        if(isset($data['key'])) {
            $device = DB::table('devices')->where('key', 'LIKE', $data['key'])->first();
            if($device->status == 'off'){
                $users = DB::select("SELECT `id_user` FROM `notifications_devices` WHERE `value` = 1 and `id_device` = :id", ['id' => $device->id]);
                foreach ($users as $userid){
                    $user = DB::select("SELECT * FROM `users` WHERE `id` = :id ", ['id' => $userid->id_user]);
                    Mail::raw('Статус устройства '.$device->name.' on', function ($message) use ($user) {
                        $message->from('ran@impuls-perm.ru', 'meteo');
                        $message->to($user[0]->email)->subject('Статус устройства изменился');
                    });
                }
            }
            $timestamp = date('Y-m-d H:i:s');
            DB::table('devices')
                ->where('id', '=',$device->id)
                ->update(array('status' => 'on', /*'host'=>$request->ip(), */'lastUpdate' => $timestamp));

            foreach($data as $param_name => $value){
                if(strtoupper($param_name) != 'KEY'){
                    $param = DB::table('devices_params')
                        ->where('name', 'LIKE', $param_name)
                        ->where('id_device', '=', $device->id)
                        ->first();
                    if(!isset($param->id)) {
                        $paramid = DB::table('devices_params')->insertGetId(
                            array('id_device' => $device->id, 'name' => $param_name, 'label' => $param_name, 'value'=>$value)
                        );
                        $param = DB::table('devices_params')
                            ->where('id', '=', $paramid)
                            ->first();
                    } else {
                        DB::table('devices_params')
                            ->where('id', $param->id)
                            ->update(array('value' => $value));
                    }
                    $paramid = $param->id;

                    $table_name = "ParamsData".$paramid;
                    if(!Schema::hasTable($table_name)){
                        if(gettype($value)=="string") {
                            Schema::create($table_name, function (Blueprint $table) {
                                $table->increments('id');
                                $table->string('value', 255);
                                $table->timestamp('time');
                            });
                        } else {
                            Schema::create($table_name, function(Blueprint $table)
                            {
                                $table->increments('id');
                                $table->float('value')->nullable();
                                $table->timestamp('time');
                            });
                        }
                    }

                    DB::table($table_name)->insert(
                        array('value' => $value, 'time' => $timestamp)
                    );

                    $notificationsParams = DB::select("SELECT * FROM `notifications_params` WHERE (`id_param` = ? AND `condition`='>=' AND ? >= `value` and `isSend`=0) OR (`id_param` = ? AND `condition`='<=' AND ? <= `value` and `isSend`=0)",
                        [$paramid, $value, $paramid, $value]);
                    foreach ($notificationsParams as $notification){
                        $user = DB::select("SELECT * FROM `users` WHERE `id` = :id ", ['id' => $notification->id_user]);
                        Mail::raw('Значение параметра '.$param->label.'('.$device->name.') '.$value.' '.$notification->condition.' '.$notification->value, function ($message) use ($user) {
                            $message->from('ran@impuls-perm.ru', 'meteo');
                            $message->to($user[0]->email)->subject('Значение параметра');
                        });
                        DB::table('notifications_params')
                            ->where('id', '=',$notification->id)
                            ->update(array('isSend' => 1));
                    }

                    $notificationsParams = DB::select("SELECT * FROM `notifications_params` WHERE (`id_param` = ? AND `condition`='>=' AND ? < `value` and `isSend`=1) OR (`id_param` = ? AND `condition`='<=' AND ? > `value` and `isSend`=1)",
                        [$paramid, $value, $paramid, $value]);
                    foreach ($notificationsParams as $notification){
                        $user = DB::select("SELECT * FROM `users` WHERE `id` = :id ", ['id' => $notification->id_user]);
                        Mail::raw('Значение параметра '.$param->label.'('.$device->name.') '.$value.' '.$notification->condition=='>='?'<':'>'.' '.$notification->value, function ($message) use ($user) {
                            $message->from('ran@impuls-perm.ru', 'meteo');
                            $message->to($user[0]->email)->subject('Значение параметра');
                        });
                        DB::table('notifications_params')
                            ->where('id', '=',$notification->id)
                            ->update(array('isSend' => 0));
                    }
                }
            }
            event(new DeviceEvent($device->id));
            return response()->json([
                'status' => 'success',
            ], 200);
        }
    }

    public function updateOTA(Request $request, $key){
        $path = Storage::disk('scatchs')->path($key.'.bin');//storage_path().'\\app\\storage\\scatchs\\'.$key.'.bin';
        header($_SERVER["SERVER_PROTOCOL"].' 200 OK', true, 200);
        header('Content-Type: application/octet-stream', true);
        header('Content-Disposition: attachment; filename='.basename($path));
        header('Content-Length: '.filesize($path), true);
        header('x-MD5: '.md5_file($path), true);
        readfile($path);
    }
}
