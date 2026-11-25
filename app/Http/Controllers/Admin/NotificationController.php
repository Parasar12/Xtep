<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MailConfigSetting;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller{

  public function index(){
    $this->_hasMenuAccess('NotificationSetup-Index');

    return view('admin.notification.index');
  }

}