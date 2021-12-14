<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $list = Permission::select();

        if(isset($_GET['sortColumn']) && isset($_GET['sortDirection']))
            $list = $list->orderBy($_GET['sortColumn'], $_GET['sortDirection']==='ascending'?'asc':'desc');
        if(isset($_GET['findText'])){
            $list = $list->orWhere('name', 'like', '%'.$_GET['findText'].'%');
        }
        $list = $list->paginate(100);
        
        return response()->json(
            $list
            , 200);
    }

    public function store(Request $request)
    {
        
    }

    public function destroy($id)
    {
        
    }
}
