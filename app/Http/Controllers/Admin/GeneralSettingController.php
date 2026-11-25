<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{

  public function index()
  {
    $this->_hasMenuAccess('MasterSetup-SiteSetting');

    return view('admin.general-settings.edit');
  }


  public function store(Request $request)
  {
    $this->_hasMenuAccess('MasterSetup-SiteSetting');

    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'footer_text' => 'required',
      'welcome_text' => 'required',
      'logo' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
      'logo_size' => 'required',
      'favicon' => 'mimes:ico,ICO,png,PNG|max:2048',
      'phone' => 'required',
      'email' => 'required|email',
      'opening_hours' => 'required',
      'address' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
    } else {

      GeneralSetting::where('key', 'title')->update([
        'value' => $request->title
      ]);

      GeneralSetting::where('key', 'welcome_text')->update([
        'value' => $request->welcome_text
      ]);

      GeneralSetting::where('key', 'footer_text')->update([
        'value' => $request->footer_text
      ]);

      GeneralSetting::where('key', 'phone')->update([
        'value' => $request->phone
      ]);
      GeneralSetting::where('key', 'email')->update([
        'value' => $request->email
      ]);
      GeneralSetting::where('key', 'address')->update([
        'value' => $request->address
      ]);
      GeneralSetting::where('key', 'opening_hours')->update([
        'value' => $request->opening_hours
      ]);
      GeneralSetting::where('key', 'facebook_page_link')->update([
        'value' => $request->fb_page_link
      ]);

      GeneralSetting::where('key', 'bill_footer_text')->update([
        'value' => $request->bill_footer_text
      ]);

      GeneralSetting::where('key', 'logo_size')->update([
        'value' => $request->logo_size
      ]);

      if ($request->file('logo')) {
        $extension = $request->logo->extension();
        $imageName = date('m-d-Y_H-i-s') . $request->file('logo')->getClientOriginalName();

        $request->file('logo')->move(public_path('/uploads/logo'), $imageName);

        GeneralSetting::where('key', 'logo')->update([
          'value' => $imageName
        ]);
      }

      if ($request->file('favicon')) {
        $extension = $request->favicon->extension();
        $favName = date('m-d-Y_H-i-s') . $request->file('favicon')->getClientOriginalName();

        $request->file('favicon')->move(public_path('/uploads/favicon'), $favName);

        GeneralSetting::where('key', 'favicon')->update([
          'value' => $favName
        ]);
      }

      return response()->json(['status' => 1, 'msg' => "General Settings Updated Successfully"]);
    }
  }

}
