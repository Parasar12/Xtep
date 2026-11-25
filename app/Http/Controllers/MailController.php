<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MailConfigSetting;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller{

  public function index(){
    $this->_hasMenuAccess('MasterSetup-MailConfiguration');

    $config = MailConfigSetting::first();
    return view('admin.mail-settings.edit',compact('config'));
  }

  public function store(Request $request){
    $this->_hasMenuAccess('MasterSetup-MailConfiguration');

    $validator = Validator::make($request->all(), [
      'mail_from' => 'required|email',
      'name' => 'required|max:255',
      'host' => 'required',
      'port' => 'required|numeric',
      'username' => 'required',
      'password' => 'required',
      'encryption' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
    }else{

      MailConfigSetting::truncate();

      $config = new MailConfigSetting();
      $config->email_from = $request->mail_from;
      $config->name = $request->name;
      $config->mail_host = $request->host;
      $config->port = $request->port;
      $config->username = $request->username;
      $config->password = $request->password;
      $config->encryption = $request->encryption;
      $config->save();

      return response()->json(['status' => 1, 'msg' => "Mail Config Updated Successfully."]);
    }

  }
}
