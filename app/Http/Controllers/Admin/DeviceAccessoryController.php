<?php

namespace App\Http\Controllers\Admin;


use App\Models\DeviceAccessory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DeviceAccessoryController extends Controller
{
  //
  public function index()
  {
    $this->_hasMenuAccess('DeviceManagement-Accessories');
    return view('admin.device_management.accessories.index');
  }

  public function store(Request $request)
  {
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'position' => 'required',
    ]);

    if ($validator->fails()) {
      $output = array(
        'result' => $validator->errors()->toArray(),
      );

      throw new Exception("Form Validation Error");
    } else {

      DB::beginTransaction();

      try {

        if ($request->file('image')) {
          $extension = $request->image->extension();
          $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

          $request->file('image')->move(public_path('/uploads/device_management'), $imageName);
        }

        DeviceAccessory::create([
          'name' => $request->name,
          'position' => $request->position,
          'featured_image' => $imageName,
          'is_active' => 1,

        ]);

        DB::commit();

        $output = array(
          'response' => True,
          'msg' => "Device Accessory Created Successfully",
          'result' => "",
        );
      } catch (Exception $e) {
        DB::rollBack();

        $output['response'] = FALSE;
        $output['msg'] = $e->getMessage();
      }
    }

    return Response::json($output);
  }

  public function getAccessories(Request $request)
  {
    $this->_hasMenuAccess('DeviceManagement-Accessories');


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
    $totalRecords = DeviceAccessory::select('count(*) as allcount')->count();
    $totalRecordswithFilter = DeviceAccessory::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

    /*Fetch records*/
    if ($request->length == -1) {
      $records = DeviceAccessory::orderBy($columnName, $columnSortOrder)
        ->where([
          ['device_accessories_master.name', 'like', '%' . $searchValue . '%'],
          ['is_active', 1],
        ])
        ->select('position', 'name', 'id')
        ->take($rowperpage)
        ->get();
    } else {
      $records = DeviceAccessory::orderBy($columnName, $columnSortOrder)
        ->where([
          ['device_accessories_master.name', 'like', '%' . $searchValue . '%'],
          ['is_active', 1],
        ])
        ->select('position', 'name', 'id')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    }

    $data_arr = array();

    $i = 1;
    foreach ($records as $record) {
      $position = $record->position;
      $name = $record->name;
      $id = $record->id;

      $data_arr[] = array(
        "id" => $i,
        "position" => $position,
        "name" => $name,
        'action' => "<a class='edit_problem btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('accessories.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_accessories btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
      );
      $i++;
    }

    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordswithFilter,
      "aaData" => $data_arr,
    );

    print(json_encode($response));
    exit();
  }

  public function edit($id)
  {
    $this->_hasMenuAccess('DeviceManagement-Accessories');

    $accessory = DeviceAccessory::where('id', $id)->first();
    return view('admin.device_management.accessories.edit', compact('accessory'));
  }

  public function updateAccessories(Request $request, $id)
  {

    $this->_hasMenuAccess('DeviceManagement-Accessories');

    $validator = Validator::make($request->all(), [
      'name' => 'required|max:255' . $id,
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
    } else {

      if ($request->file('image')) {
        $extension = $request->image->extension();
        $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

        $request->file('image')->move(public_path('/uploads/device_management'), $imageName);
      }

      DeviceAccessory::where('id', $id)->update([
        'name' => $request->name,
        'position' => $request->position,
        'featured_image' => $request->file('image') ? $imageName : '',
      ]);

      return response()->json(['status' => 1, 'msg' => 'Device Accessory Updated Successfully.']);
    }
  }

  public function deleteAccessory(Request $request)
  {
    $this->_hasMenuAccess('DeviceManagement-Accessories');

    DeviceAccessory::where('id', $request->accessoryId)->update([
      'is_active' => 0
    ]);

    $output = array(
      'response' => True,
      'msg' => " Device Accessory Delete Successfully",
      'result' => "",
    );

    return Response::json($output);
  }
}
