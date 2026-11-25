<?php

namespace App\Http\Controllers\Admin;

use App\Models\DeviceProblem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DeviceManagementController extends Controller
{
  //

  public function device_problem()
  {
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    return view('admin.device_management.problem.index');
  }

  public function getProblems(Request $request)
  {
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

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
    $totalRecords = DeviceProblem::select('count(*) as allcount')->count();
    $totalRecordswithFilter = DeviceProblem::select('count(*) as allcount')->where('problem_name', 'like', '%' . $searchValue . '%')->count();

    /*Fetch records*/
    if ($request->length == -1) {
      $records = DeviceProblem::orderBy($columnName, $columnSortOrder)
        ->where([
          ['device_problem_master.problem_name', 'like', '%' . $searchValue . '%'],
          ['is_active', 1],
        ])
        ->select('position', 'problem_name', 'id')
        ->take($rowperpage)
        ->get();
    } else {
      $records = DeviceProblem::orderBy($columnName, $columnSortOrder)
        ->where([
          ['device_problem_master.problem_name', 'like', '%' . $searchValue . '%'],
          ['is_active', 1],
        ])
        ->select('position', 'problem_name', 'id')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    }

    $data_arr = array();

    $i = 1;
    foreach ($records as $record) {
      $position = $record->position;
      $name = $record->problem_name;
      $id = $record->id;

      $data_arr[] = array(
        "id" => $i,
        "position" => $position,
        "problem_name" => $name,
        'action' => "<a class='edit_problem btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('problems.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_problems btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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

  public function store(Request $request)
  {
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    $validator = Validator::make($request->all(), [
      'problem_name' => 'required',
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

        DeviceProblem::create([
          'problem_name' => $request->problem_name,
          'position' => $request->position,
          'featured_image' => $imageName,
          'is_active' => 1,
          'created_by' => Auth::user()->id
        ]);

        DB::commit();

        $output = array(
          'response' => True,
          'msg' => "Device Problem Created Successfully",
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

  public function edit($id)
  {
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    $problem = DeviceProblem::where('id', $id)->first();
    return view('admin.device_management.problem.edit', compact('problem'));
  }

  public function updateProblem(Request $request, $id)
  {

    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    $validator = Validator::make($request->all(), [
      'problem_name' => 'required|max:255' . $id,
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
    } else {
      if ($request->file('image')) {
        $extension = $request->image->extension();
        $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

        $request->file('image')->move(public_path('/uploads/device_management'), $imageName);
      }

      DeviceProblem::where('id', $id)->update([
        'problem_name' => $request->problem_name,
        'position' => $request->position,
        'featured_image' => $request->file('image') ? $imageName : '',
        'created_by' => Auth::user()->id
      ]);

      return response()->json(['status' => 1, 'msg' => 'Device Problem Updated Successfully.']);
    }
  }

  public function deleteProblem(Request $request){
    $this->_hasMenuAccess('DeviceManagement-ProblemList');

    DeviceProblem::where('id', $request->problemId)->update([
      'is_active'=>0
    ]);

    $output = array(
      'response' => True,
      'msg' => " Device Problem Delete Successfully",
      'result' => "",
    );

    return Response::json($output);
  }
}
