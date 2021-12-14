<?php

namespace App\Http\Controllers;

use App\Events\DevicesEvent;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $list = User::with('permissions', 'roles', 'devices', 'cameras');
        $list = (isset($request->all) && $request->all === 'true') ? $list->paginate(999999) : $list->paginate(30);

        return response()->json(
            $list,
            200
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ], [
            'name.required' => 'Имя не может быть пустым.',
            'email.required' => 'Email не может быть пустым.',
            'password.required' => 'Пароль не может быть пустым.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["errors" => $validator->errors()->all()],
                422
            );
        }

        $data = json_decode($request->getContent(), true);
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        $user->save();
        $user->permissions()->sync($data['permissions_ids']);
        $user->roles()->sync($data['roles_ids']);
        $user->devices()->sync($data['devices_ids']);
        $user->cameras()->sync($data['cameras_ids']);

        return response()->json(
            $user,
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = User::with('permissions', 'roles', 'devices', 'cameras')->find($id);

        return response()->json(
            $item,
            200
        );
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ], [
            'name.required' => 'Имя не может быть пустым.',
            'email.required' => 'Email не может быть пустым.'
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["errors" => $validator->errors()->all()],
                422
            );
        }

        $user = User::with('permissions', 'devices')->where('id', $id)->first();
        $data = json_decode($request->getContent(), true);
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (isset($data['password']) && ($data['password'] !== ''))
            $user->password = bcrypt($data['password']);
        $user->permissions()->sync($data['permissions_ids']);
        $user->roles()->sync($data['roles_ids']);
        $user->devices()->sync($data['devices_ids']);
        $user->cameras()->sync($data['cameras_ids']);
        $user->save();
        return response()->json([
            'user' => User::with('permissions', 'devices')->where('id', $id)->first()
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (DB::delete('DELETE FROM `users` WHERE `id`=?', [$id]))
            return response()->json([
                'status' => 'success'
            ], 200);
        else return response()->json([
            'status' => 'error'
        ], 500);
    }
}
