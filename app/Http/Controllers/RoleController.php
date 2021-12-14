<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $list = Role::with('permissions', 'users');

        if (isset($_GET['sortColumn']) && isset($_GET['sortDirection']))
            $list = $list->orderBy($_GET['sortColumn'], $_GET['sortDirection'] === 'ascending' ? 'asc' : 'desc');
        if (isset($_GET['findText'])) {
            $list = $list->orWhere('name', 'like', '%' . $_GET['findText'] . '%');
        }
        $list = (isset($request->all) && $request->all === 'true') ? $list->paginate(999999) : $list->paginate(30);

        return response()->json(
            $list,
            200
        );
    }

    public function show(Request $request, $id)
    {
        $item = Role::with('permissions', 'users')->find($id);

        return response()->json(
            $item,
            200
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'Наименование не может быть пустым.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["errors" => $validator->errors()->all()],
                422
            );
        }

        $item = Role::create(
            [
                'name' => $request->input('name'),
                'slug' => $request->input('name'),
            ]
        );

        $item->users()->sync($request->users_ids);
        $item->permissions()->sync($request->permissions_ids);

        return response()->json(
            $item,
            200
        );
    }

    public function update(Request $request, $id)
    {
        $item = Role::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ], [
            'name.required' => 'Название роли не может быть пустым.',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ["error" => $validator->errors()->all()],
                422
            );
        }

        $item->name = $request->all()['name'];
        $item->slug = $request->all()['name'];
        
        $item->save();

        $item->users()->sync($request->users_ids);
        $item->permissions()->sync($request->permissions_ids);

        return response()->json(
            $item,
            200
        );
    }

    public function destroy($id)
    {
        $item = Role::find($id);
        $item->delete();

        return response()->json(
            "success",
            200
        );
    }
}
