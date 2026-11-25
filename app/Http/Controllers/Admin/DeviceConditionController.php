<?php

namespace App\Http\Controllers\Admin;


use App\Models\DeviceCondition;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class DeviceConditionController extends Controller
{
    //
    public function index(){
        $this->_hasMenuAccess('DeviceManagement-ConditionList');

        return view('admin.device_management.condition.index');
    }
    public function getConditions(Request $request)
    {
        $this->_hasMenuAccess('DeviceManagement-ConditionList');

  
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
      $totalRecords = DeviceCondition::select('count(*) as allcount')->count();
      $totalRecordswithFilter = DeviceCondition::select('count(*) as allcount')->where('question', 'like', '%' . $searchValue . '%')->count();
  
      /*Fetch records*/
      if ($request->length == -1) {
        $records = DeviceCondition::orderBy($columnName, $columnSortOrder)
          ->where([
            ['device_condition_master.question', 'like', '%' . $searchValue . '%'],
            ['is_active', 1],
          ])
          ->select('position', 'question','hint', 'id')
          ->take($rowperpage)
          ->get();
      } else {
        $records = DeviceCondition::orderBy($columnName, $columnSortOrder)
          ->where([
            ['device_condition_master.question', 'like', '%' . $searchValue . '%'],
            ['is_active', 1],
          ])
          ->select('position', 'question','hint', 'id')
          ->skip($start)
          ->take($rowperpage)
          ->get();
      }
  
      $data_arr = array();
  
      $i = 1;
      foreach ($records as $record) {
        $position = $record->position;
        $question = $record->question;
        $hint = $record->hint;
        $id = $record->id;
  
        $data_arr[] = array(
          "id" => $i,
          "position" => $position,
          "question" => $question,
          "hint" => $hint,
          'action' => "<a class='edit_problem btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('condition.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_condition btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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
        'question' => 'required',
        'hint' => 'required',
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
  
          
          DeviceCondition::create([
            'question' => $request->question,
            'hint' => $request->hint,
            'position' => $request->position,
            'is_active' => 1,
            'created_by'=>Auth::user()->id
           
          ]);
  
          DB::commit();
  
          $output = array(
            'response' => True,
            'msg' => "Device Condition Created Successfully",
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
        $this->_hasMenuAccess('DeviceManagement-ConditionList');

      $condition = DeviceCondition::where('id', $id)->first();
      return view('admin.device_management.condition.edit', compact('condition'));
    }

    public function updateCondition(Request $request, $id)
    {
  
        $this->_hasMenuAccess('DeviceManagement-ConditionList');
      
  
      $validator = Validator::make($request->all(), [
        'question' => 'required|max:255' . $id,
      ]);
  
      if ($validator->fails()) {
        return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
      } else {
  
  
        DeviceCondition::where('id', $id)->update([
          'question' => $request->question,
          'hint' => $request->hint,
          'created_by'=>Auth::user()->id,
         
        ]);
  
        return response()->json(['status' => 1, 'msg' => 'Device Condition Updated Successfully.']);
      }
    }

    public function deleteCondition(Request $request)
    {
        $this->_hasMenuAccess('DeviceManagement-ConditionList');
  
      DeviceCondition::where('id', $request->conditionId)->update([
        'is_active' => 0
      ]);
  
      $output = array(
        'response' => True,
        'msg' => " Device Condition Delete Successfully",
        'result' => "",
      );
  
      return Response::json($output);
    }

}
