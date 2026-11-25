<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;


use App\Models\Menu;
use App\Models\Permission;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Response;
use Input;


class MenuController extends Controller
{

    function index()
    {
        $this->_hasMenuAccess('UserDashboard');

        return view('admin.menu.index');
    }

    public function getMenuList()
    {
        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $data = DB::table('menu_master')
                ->orderBy('pre_menu_id', 'asc')
                ->orderBy('menu_index', 'asc')
                ->where('is_active', 'y')
                ->select('menu_id', 'menu_code', 'menu_name', 'pre_menu_id', 'menu_type', 'menu_index')
                ->get();

            $menuData = json_decode(json_encode($data), true);

            $result = array();
            foreach ($menuData as $key => $value) {
                $value['enc_menu_id'] = urlencode(base64_encode($value['menu_id']));
                $result[$value['menu_id']] = $value;
            }

            $dataOutput = '';
            $dataOutput = arrayHierarchy($result, 'pre_menu_id', 'menu_id');

            $output = array(
                'result' => $dataOutput,
                'response' => TRUE,
                'msg' => '',
            );
        } catch (Exception $e) {
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    function getAddMenuForm()
    {
        $output = array(
            'result' => view('admin.menu.add')->render(),
            'response' => TRUE,
            'msg' => '',
        );

        return Response::json($output);
    }

    function addMenu(Request $request)
    {
        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $validator = Validator::make($request->all(), [
                'menu_code' => 'required|max:255',
                'menu_name' => 'required|max:255',
                'menu_type' => 'required',
                'menu_index' => 'required',
            ]);

            if ('outer' == $request->menu_type) {
                $validator = Validator::make($request->all(), [
                    'route' => 'required',
                ]);
            }

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                DB:: beginTransaction();

                $menuData = DB::table('menu_master')->max('menu_id');
                $menuId = $menuData + 1;

                $menu = new Menu();

                $menu->menu_id = $menuId;
                $menu->menu_code = $request->menu_code;
                $menu->menu_name = $request->menu_name;
                $menu->menu_index = $request->menu_index;
                $menu->menu_type = $request->menu_type;
                $menu->icon_class = $request->icon_class;
                $menu->route = $request->route;
                $menu->is_active = 'y';
                $menu->created_by = auth()->user()->id;
                $menu->pre_menu_id = (empty($request->parent_menu)) ? 0 : base64_decode(urldecode($request->parent_menu));
                $menu->save();

                /*User Permission*/
                $permission = new Permission();

                $permission->menu_id = $menuId;
                $permission->user_id = auth()->user()->id;
                $permission->created_date = date('Y-m-d H:i:s');
                $permission->created_by = auth()->user()->id;
                $permission->save();

                DB::commit();

                $output = array(
                    'response' => True,
                    'msg' => "Menu added Successfully",
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

    function getEditMenuForm(Request $request)
    {
        $encMenuId = $request->get('menuId');
        $menuId = base64_decode(urldecode($encMenuId));

        $menu = DB::select(" SELECT a.menu_code, a.menu_id, a.menu_name, a.pre_menu_id, b.menu_name AS parent_menu, a.menu_type, a.route, a.icon_class, a.menu_index
      FROM menu_master a
      LEFT JOIN menu_master b ON b.menu_id = a.pre_menu_id
      WHERE a.menu_id = '$menuId' ");

        $output = array(
            'result' => view('admin.menu.edit', compact('menu'))->render(),
            'response' => TRUE,
            'msg' => '',
        );

        return Response::json($output);
    }

    function editMenu(Request $request)
    {
        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $validator = Validator::make($request->all(), [
                'menu_code' => 'required|max:255',
                'menu_name' => 'required|max:255',
                'menu_type' => 'required',
                'menu_index' => 'required',
            ]);

            if ('outer' == $request->menu_type) {
                $validator = Validator::make($request->all(), [
                    'route' => 'required',
                ]);
            }

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                $menu = array(
                    'menu_code' => $request->menu_code,
                    'menu_name' => $request->menu_name,
                    'menu_index' => $request->menu_index,
                    'menu_type' => $request->menu_type,
                    'icon_class' => $request->icon_class,
                    'pre_menu_id' => (empty($request->parent_menu)) ? 0 : base64_decode(urldecode($request->parent_menu)),
                    'route' => $request->route,
                );

                DB::table('menu_master')
                    ->where('menu_id', $request->menuId)
                    ->update($menu);

                $output = array(
                    'response' => True,
                    'msg' => "Menu Updated Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }


    function viewMenuDetail(Request $request)
    {
        $encMenuId = $request->get('menuId');
        $menuId = base64_decode(urldecode($encMenuId));

        $menu = DB::select(" SELECT a.menu_code, a.menu_name, b.menu_name AS parent_menu, a.menu_type, a.route, a.icon_class, a.menu_index FROM menu_master a
      LEFT JOIN menu_master b ON b.menu_id = a.pre_menu_id
      WHERE a.menu_id = '$menuId' ");

        $output = array(
            'result' => view('admin.menu.view', compact('menu'))->render(),
            'response' => TRUE,
            'msg' => '',
        );
        return Response::json($output);
    }
}
