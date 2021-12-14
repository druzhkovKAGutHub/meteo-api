<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(Request $request){
        $list = Permission::select();

        if (isset($_GET['sortColumn']) && isset($_GET['sortDirection']))
            $list = $list->orderBy($_GET['sortColumn'], $_GET['sortDirection'] === 'ascending' ? 'asc' : 'desc');
        if (isset($_GET['findText'])) {
            $list = $list->orWhere('name', 'like', '%' . $_GET['findText'] . '%');
        }
        if (isset($_GET['all']) && $_GET['all']) {
            $list = $list->paginate(99999);
        } else $list = $list->paginate(30);

        return response()->json(
            $list,
            200
        );
    }
}
