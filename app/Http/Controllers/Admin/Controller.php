<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\DB;

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
}
