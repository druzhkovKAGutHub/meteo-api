<?php

namespace App\Http\Controllers;

use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\CameraHasGroup;
use App\Models\Device;
use App\Models\CamerasGroup;
use App\Models\CamerasGroupHasUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CamerasGroupController extends Controller
{

    public function index(Request $request)
    {
        $list = CamerasGroup::with('cameras', 'users');
        $list = $list->whereIn('id', CamerasGroupHasUser::select('group_id')->where('user_id', Auth::user()->id));
        $list = (isset($request->all) && $request->all==='true') ? $list->paginate(999999) : $list->paginate(12);

        return response()->json(
            $list
            , 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $group = new CamerasGroup([
            'name' => $data['name']
        ]);
        $group->save();
        $group->cameras()->sync($data['cameras_ids']);
        $group->users()->sync($data['users_ids']);
        return response()->json(
            $group
            , 200);
    }

     /**
     * Display the specified resource.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = CamerasGroup::with('cameras', 'users')->find($id);
        return response()->json(
            $group
            , 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $group = CamerasGroup::find($id);
        $data = json_decode($request->getContent(), true);
        $group->name = $data['name'];
        $group->cameras()->sync($data['cameras_ids']);
        $group->users()->sync($data['users_ids']);
        $group->save();
        return response()->json(
            $group, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = CamerasGroup::destroy($id);
        if ($res) {
            return response()->json([
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error'
            ], 500);
        }
    }
}
