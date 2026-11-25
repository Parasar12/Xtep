<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function _checkMenuAccess($menuCode)
    {

        $menu = DB::table('user_permission')
            ->join('menu_master', 'user_permission.menu_id', '=', 'menu_master.menu_id')
            ->where('menu_master.is_active', 'y')
            ->where('user_permission.user_id', auth()->user()->id)
            ->where('menu_master.menu_code', $menuCode)
            ->select('menu_master.menu_code')
            ->first();

        if (!empty($menu->menu_code)) {
            return TRUE;
        }

        return FALSE;
    }

    protected function _hasMenuAccess($menuCode)
    {
        if (!$this->_checkMenuAccess($menuCode)) {
            return abort(403);
        }
    }

    public function privacy()
    {
        $data = DB::table('global_config')->where('title', 'privacy_policy')->first();
        return view('admin.privacy.index', compact('data'));
    }

    public function toc()
    {
        $data = DB::table('global_config')->where('title', 'terms_and_conditions')->first();
        return view('admin.privacy.index', compact('data'));
    }

    public function storePtoc(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:200',
                'description' => 'required'
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {

                DB::table('global_config')->where('title', '=', $request->title)->update([
                    'value' => $request->description
                ]);

                $output = array(
                    'response' => true,
                    'msg' => "Save Successfully",
                    'result' => "",
                );
            }
        } catch (Exception  $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);

    }

    public function clearQuickOrderData(){
        Session::forget('quick_order');
        Session::forget('quickProducts');
    }

    function rand_color()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
