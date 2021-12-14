<?php

namespace App\Http\Controllers;

use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DevicesGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DevicesGroupController extends Controller
{

    public function index()
    {
        $groups = DevicesGroup::paginate(15);

        return response()->json(
            $groups
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
        $group = new DevicesGroup([
            'name' => $data['name']
        ]);
        $group->save();
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
        $group = DevicesGroup::find($id);
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
        $group = DevicesGroup::find($id);
        $data = json_decode($request->getContent(), true);
        $group->name = $data['name'];
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
        $res = DevicesGroup::destroy($id);
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
