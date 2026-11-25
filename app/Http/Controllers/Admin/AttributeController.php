<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Attribute;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class AttributeController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('ProductSetup-Attribute');

        return view('admin.attributes.index');
    }

    public function getAttributes(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Attribute');

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
        $totalRecords = Attribute::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Attribute::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Attribute::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['attributes.name', 'like', '%' . $searchValue . '%'],
                    ['is_active', 1],
                ])
                ->select('id', 'name')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Attribute::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['attributes.name', 'like', '%' . $searchValue . '%'],
                    ['is_active', 1],
                ])
                ->select('id', 'name')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;

            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('attributes.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_attribute btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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
        $this->_hasMenuAccess('ProductSetup-Attribute');

        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
            ]);
    
            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {
                $attribute = new Attribute();
                $attribute->name = $request->name;
                $attribute->save();
    
                $output = array(
                    'response' => True,
                    'msg' => "Attribute Created Successfully",
                    'result' => "",
                );
            }

        }catch(Exception $e){
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function edit($id)
    {
        $this->_hasMenuAccess('ProductSetup-Attribute');

        $attribute = Attribute::where('id', $id)->first();
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $this->_hasMenuAccess('ProductSetup-Attribute');

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:attributes,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $attribute = Attribute::find($id);
            $attribute->name = $request->name;
            $attribute->save();

            return response()->json(['status' => 1, 'msg' => 'Attribute Updated Successfully.']);
        }
    }

    function deleteAttributes(Request $request)
    {

        $this->_hasMenuAccess('ProductSetup-Attribute');

        Attribute::where('id', $request->attributeId)->update([
            'is_active' => 0
        ]);

        $output = array(
            'response' => True,
            'msg' => " Attribute Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    public function search(Request $request): JsonResponse
    {
        $data = Attribute::select('id', 'name')
            ->where('name', 'like', '%' . $request->keyword . '%')
            ->where('is_active', 1)
            ->limit(10)
            ->get();

        $result = array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $row ['id'] = $value->id;
                $row['text'] = $value->name;

                $result[] = $row;
            }
        }

        $output = array(
            'response' => true,
            'msg' => '',
            'result' => array('results' => $result),
        );

        return Response::json($output);
    }

}
