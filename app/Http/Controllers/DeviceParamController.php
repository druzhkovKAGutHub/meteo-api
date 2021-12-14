<?php

namespace App\Http\Controllers;

use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\DeviceParams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DeviceParamController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $item = DeviceParams::findOrFail($id);
        $item->label = $request->label;
        $item->color = $request->color;
        $item->classIcon = $request->classIcon;
        $item->isHidden = $request->isHidden;
        $item->id_unit = $request->id_unit;
        $item->isFavorit = $request->isFavorit; 

        if($item->save())
            return response()->json([
                'status' => 'success'
            ], 200);
        else return response()->json([
            'status' => 'error'
        ], 500);
    }

    public function getData(Request $request, $id_device){
        $params = DB::select('select id, name, label, color, classIcon, id_unit from devices_params where id_device = :id', ['id'=>$id_device]);
        $paramsRez = [];
        $paramsRezTable = [];
        foreach ($params as $param){
            $table_name = "ParamsData".$param->id;
            if(Schema::hasTable($table_name)) {
                $vals = DB::select('SELECT time, value FROM ' . $table_name
                        . " where time between \"" . date("Y-m-d 00:00:00", strtotime($request->get('fromDate')) )
                        . "\" and \"" . date("Y-m-d 23:59:59", strtotime($request->get('toDate'))) ."\" LIMIT ".$request->get('limit'));
                foreach( $vals as $val ){
                    $paramsRez[$param->id][] = $val;
                    $paramsRezTable[$val->time][$param->label] = $val->value;
                }
            }
        }
        $table = [];
        foreach ($paramsRezTable as $time => $vals){
            $table[] = array_merge(['Время'=>$time], $vals);
        }
        return response()->json(
            [
                'data' => $paramsRez,
                'params' => $params,
                'table' => $table
            ]
            , 200);
    }

}
