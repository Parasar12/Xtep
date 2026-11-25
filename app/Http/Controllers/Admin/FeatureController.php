<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Feature;
use Illuminate\Support\Facades\Validator;

use Exception;
use Illuminate\Support\Facades\Response;

class FeatureController extends Controller{

  public function index(){
    $this->_hasMenuAccess('HomePageSetup-FeatureSetup');

    $features = Feature::orderBy('feature', 'asc')->get();
    return view('admin.features.edit', compact('features'));
  }

  public function store(Request $request){

  }

  public function edit($id){

  }

  public function update(Request $request){
    $this->_hasMenuAccess('HomePageSetup-FeatureSetup');

    try{

      $titles = $request->title;
      $subtitles = $request->sub_title;
      $spans = $request->span;
      $icons = $request->icon;

      $total = count($titles);

      for ($i = 0; $i < $total; $i++) {
        $j = $i + 1;
        $feature = Feature::where('feature', $j)->update([
          'title' => $titles[$i],
          'sub_title' => $subtitles[$i],
          'span' => $spans[$i],
          'icon' => $icons[$i]
        ]);
      }

      $output = array(
        'response' => True,
        'msg' => "Features Updated Successfully",
        'result' => "",
      );
    }catch (Exception $e) {
      $output['response'] = FALSE;
      $output['msg'] = $e->getMessage();
    }

    return Response::json($output);    
  }
}
