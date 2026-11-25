<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;


use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Exception;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PermissionController extends Controller
{

    function index()
    {
        $this->_hasMenuAccess('Permission-User');

        return view('admin.permission.index');
    }

    function getUserList(Request $request)
    {
        $this->_hasMenuAccess('Permission-User');

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        /*Total records*/
        $totalRecords = User::select('count(*) as allcount')->where('is_admin', 1)->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')->where('is_admin', 1)->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 1)
                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->take($rowperpage)
                ->get();
        } else {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 1)
                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $user = ($record->is_admin == 1) ? 'Admin' : 'Customer';

            $name = $record->name;
            $email = $record->email;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "is_admin" => $user,
                "name" => $name,
                "email" => $email,
                "is_active" => $status,
                'action' => "<a class='assign_permission btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-userid='" . $id . "'><i class='fa fa-cog'></i></a>"
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        print(json_encode($response));
        exit();
    }

    function assignPermissionIndex(Request $request)
    {
        $this->_hasMenuAccess('Permission-User');

        $userId = $request->get('userId');

        $user = User::select('name', 'email')->where('is_admin', 1)->where('id', $userId)->first();

        $menuMaster = DB::table('menu_master')
            ->orderBy('pre_menu_id', 'asc')
            ->orderBy('menu_index', 'asc')
            ->where('is_active', 'y')
            ->select('menu_id', 'menu_code', 'menu_name', 'icon_class', 'pre_menu_id', 'menu_type', 'menu_index')
            ->get();

        $menuData = json_decode(json_encode($menuMaster), true);

        $result = array();
        foreach ($menuData as $key => $value) {
            $value['enc_menu_id'] = urlencode(base64_encode($value['menu_id']));
            $result[$value['menu_id']] = $value;
        }

        $menu = arrayHierarchy($result, 'pre_menu_id', 'menu_id');

        /*Existing User Permission*/
        $permission = DB::table('user_permission')
            ->where('user_id', $userId)
            ->select('menu_id')
            ->get();

        $permissionData = json_decode(json_encode($permission), true);

        $userPermission = array();
        foreach ($permissionData as $key => $row) {
            $userPermission[] = $row['menu_id'];
        }

        $output = array(
            'result' => view('admin.permission.assign', compact('user', 'menu', 'userPermission', 'userId'))->render(),
            'response' => TRUE,
            'msg' => '',
        );

        return Response::json($output);
    }

    function saveAssignedPermission(Request $request)
    {
        $this->_hasMenuAccess('Permission-User');

        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $validator = Validator::make($request->all(), [
                'menu_id' => 'required',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                DB:: beginTransaction();

                DB::table('user_permission')->where('user_id', $request->userId)->delete();


                $menuArray = $request->menu_id;

                foreach ($menuArray as $key => $value) {
                    $permission = new Permission();

                    $data = DB::table('user_permission')->max('permission_id');
                    $permissionId = $data + 1;

                    $permission->permission_id = $permissionId;
                    $permission->menu_id = base64_decode(urldecode($value));
                    $permission->user_id = $request->userId;
                    $permission->created_date = date('Y-m-d H:i:s');
                    $permission->created_by = auth()->user()->id;
                    $permission->save();
                }

                DB::commit();

                $output = array(
                    'response' => True,
                    'msg' => "Permission added Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            DB::rollback();

            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }
}
