<?php

namespace App\Http\Controllers;

use App\Events\DeviceConfigEvent;
use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\DeviceHasGroup;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
      
     */
    public function index(Request $request)
    {
        $list = Device::with('params');
        if(isset($request->sortColumn) && isset($request->sortDirection))
            $list = $list->orderBy($request->sortColumn, $request->sortDirection==='ascending'?'asc':'desc');
        if(isset($request->findText)){
            $list = $list->orWhere('name', 'like', '%'.$request->findText.'%');
        }
        
        $list = (isset($request->all) && $request->all==='true') ? $list->paginate(999999) : $list->paginate(12);
        return response()->json(
            $list
            , 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $id = DB::table('devices')->insertGetId(
            array('name' => $data['name'], 'update' => $data['update'], 'key'=>$data['key'])
        );
        if(isset($data['groups'])){
            if(!in_array(0, $data['groups'])) $data['groups'][] = 0;
            foreach($data['groups'] as $groupid)
                DB::table('devices_has_group')->insert(
                    array('id_group' => $groupid, 'id_device' => $id)
                );
        } else {
            DB::table('devices_has_group')->insert(
                array('id_group' => 0, 'id_device' => $id)
            );
        }
        $device = DB::select('select * from devices where id = :id', ['id' => $id]);
        foreach ($device as $d) {
            $params = DB::select('select id, name, label, color, classIcon, id_unit, isHidden from devices_params where id_device = :id', ['id' => $id]);
            //$groups = DB::select('SELECT dg.`id`, dg.`parent_id`, dg.`name`, dg.`updated_at`, dg.`created_at` FROM `devices_group` dg, devices_has_group dgh WHERE dg.id = dhg.id_group and dhg.id_device = :id', ['id' => $id]);
            $groups = [0];
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
                    $param->value = 0;
                    $param->time = 0;
                }
                $paramsRez[$param->id] = $param;
            }
            $d->params = $paramsRez;
            $d->groups = $groups;
        }
        $request->user()->devices()->attach($id);
        //event(new DevicesEvent($id));
        return response()->json([
            'status' => 'success',
            'id' => $id,
            'data' => $device,
        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $item = Device::with('params')->findOrFail($id);

        return response()->json(
            $item
            , 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_device)
    {
        $data = json_decode($request->getContent(), true);
        $device = Device::findOrFail($id_device);//$request->user()->devices()->with('params', 'groups')->where('id', $id_device)->get()[0];
        $device->key = $data['key'];
        $device->name = $data['name'];
        $device->update = $data['update'];
        //$device->groups()->sync($data['groups']);
        if($device->save())
            return response()->json([
                'status' => 'success'
            ], 200);
        else return response()->json([
            'status' => 'error'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(DB::delete('DELETE FROM `devices` WHERE `id`=?', [$id]))
            return response()->json([
                'status' => 'success'
            ], 200);
        else return response()->json([
            'status' => 'error'
        ], 500);
    }

    public function editNotifications(Request $request, $id){
        $notifications = json_decode($request->getContent(), true);
        $user = $request->user();

        DB::delete('DELETE FROM `notifications_devices` WHERE `id_user`= :id_user and `id_device` = :id_device', ['id_user'=>$user->id, 'id_device'=>$id]);
        DB::delete('DELETE FROM `notifications_params` WHERE `id_user`= :id_user and `id_param` in (SELECT `id` FROM `devices_params` WHERE `id_device` = :id_device)', ['id_user'=>$user->id, 'id_device'=>$id]);

        if($notifications['device_status'])
            DB::table('notifications_devices')->insertGetId(
                array('id_device' => $id, 'id_user' => $user->id, 'value' => 1)
            );
        foreach($notifications['params'] as $notification){
            //DB::delete('DELETE FROM `notifications_params` WHERE `id_user`= :id_user and `id_param` = :id_param', ['id_user'=>$user->id, 'id_param'=>$notification['id_param']]);
            DB::table('notifications_params')->insertGetId(
                array('id_param' => $notification['id_param'], 'id_user' => $user->id, 'condition' => $notification['condition'], 'value' => $notification['value'])
            );
        }

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function uploadScatch(Request $request, $id){
        $file = $request->file('file');
        $device = DB::select('select * from devices where id = :id', ['id' => $id]);
        $ext = $file->getClientOriginalExtension();

        if ($file->storeAs(
            '/', $device[0]->key . '.' . $ext, 'scatchs'
          )){
            //Storage::putFileAs('scatch', $file, $device[0]->key . '.' . $ext)) {
                //event(new DeviceConfigEvent($device->key, "update",""));
                return response()->json([
                    'status' => 'success'
                ], 200);
        }
        return response()->json([
            'status' => 'error'
        ], 500);
    }

    public function sendCommand(Request $request, $id)
    {
        $data = json_decode($request->getContent(), true);
        $user = $request->user();
        $device = DB::select('select * from devices where id = :id', ['id' => $id]);
        event(new DeviceConfigEvent($device[0]->key, $data['command'], ""));
        return response()->json([
            'status' => 'success'
        ], 200);
    }

}
