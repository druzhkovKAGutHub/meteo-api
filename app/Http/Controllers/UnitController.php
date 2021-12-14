<?php

namespace App\Http\Controllers;

use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UnitController extends Controller
{

    public function index()
    {
        $units = Unit::paginate(15);

        return response()->json(
            $units
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
        $unit = new Unit([
            'name' => $data['name'],
            'short' => $data['short']
        ]);
        $unit->save();
        return response()->json(
            $unit
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
        $unit = Unit::find($id);
        return response()->json(
            $unit
            , 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        //
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
        $unit = Unit::find($id);
        $data = json_decode($request->getContent(), true);
        $unit->name = $data['name'];
        $unit->short = $data['short'];
        $unit->save();
        return response()->json(
            $unit, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $res = Unit::destroy($id);
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
