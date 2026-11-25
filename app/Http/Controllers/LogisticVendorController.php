<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\LogisticVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class LogisticVendorController extends Controller
{
    public function index()
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        return view('admin.logistic_vendors.index');
    }

    public function getLogisticVendors(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

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
        $totalRecords = LogisticVendor::select('count(*) as allcount')->count();
        $totalRecordswithFilter = LogisticVendor::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = LogisticVendor::orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%' . $searchValue . '%')
                ->orWhere('address', 'like', '%' . $searchValue . '%')
                ->take($rowperpage)
                ->get();
        } else {
            $records = LogisticVendor::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['name', 'like', '%' . $searchValue . '%'],
                ])
                ->orWhere('address', 'like', '%' . $searchValue . '%')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            $addres = $record->address;
            $url = $record->api_url;


            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "address" => $addres,
                "api_url" => $url,
                'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light'  onclick='editLogisticVendor($id)'>Edit</a>  &nbsp;&nbsp;<a class='delete_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
            );
            $i++;
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        print(json_encode($response));
        exit();
    }

    public function store(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );
                throw new \Exception("Form Validation Error");
            } else {

                LogisticVendor::create([
                    'name'=>$request->post('name'),
                    'address'=>$request->post('address'),
                    'api_base_url'=>$request->post('api_url')
                ]);

                $output = array(
                    'response' => True,
                    'msg' => "Logistic Vendor Added Successfully",
                    'result' => "",
                );
            }

        }catch(\Exception $e){
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }


    public function edit(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $vendor = LogisticVendor::where('id', $request->vendor_id)->first();
            $view = view('admin.logistic_vendors.edit', compact('vendor'))->render();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $view;

        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function update(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        $id = $request->id;

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'address' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }
            LogisticVendor::where('id',$id)->update([
                'name'=>$request->post('name'),
                'address'=>$request->post('address'),
                'api_base_url'=>$request->post('api_url')
            ]);

            $output['response'] = true;
            $output['msg'] = 'logistic vendor updated successfully';

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    function delete(Request $request)
    {

        $this->_hasMenuAccess('ProductSetup-Brand');

        LogisticVendor::where('id',$request->vendor_id)->delete();

        $output = array(
            'response' => True,
            'msg' => "Vendor  Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }
}
