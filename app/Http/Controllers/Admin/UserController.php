<?php

namespace App\Http\Controllers\Admin;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('UserManagement-User');

        return view('admin.users.index');
    }

    public function getUsers(Request $request)
    {
        $this->_hasMenuAccess('UserManagement-User');

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
        $totalRecords = User::select('count(*) as allcount')->where('is_admin', 1)->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')->where('is_admin', 1)->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 1)
//                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->take($rowperpage)
                ->get();
        } else {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 1)
//                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            if ($record->is_admin == 1) {
                $user = 'Admin';
            } else {
                $user = 'Customer';
            }
            $name = $record->name;
            $email = $record->email;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "is_admin" => $user,
                "name" => $name,
                "email" => $email,
                "contact"=>$record->contact,
                "mpin"=>$record->mpin,
                "is_active" => $status,
                'action' => "<a class='edit_user btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('users.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_user btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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

    function getCustomers()
    {
        $this->_hasMenuAccess('CustomerManagement-Index');

        return view('admin.customers.index');
    }

    public function getCustomerList(Request $request)
    {
        $this->_hasMenuAccess('CustomerManagement-Index');

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
        $totalRecords = User::select('count(*) as allcount')->where('is_admin', 0)->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')->where('is_admin', 0)->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->orWhere('users.contact', 'like', '%' . $searchValue . '%')
                ->orWhere('users.email', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 0)
//                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->take($rowperpage)
                ->get();
        } else {
            $records = User::orderBy($columnName, $columnSortOrder)
                ->where('users.name', 'like', '%' . $searchValue . '%')
                ->orWhere('users.contact', 'like', '%' . $searchValue . '%')
                ->orWhere('users.email', 'like', '%' . $searchValue . '%')
                ->where('is_admin', 0)
//                ->select('id', 'name', 'email', 'is_active', 'is_admin')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            if ($record->is_admin == 1) {
                $user = 'Admin';
            } else {
                $user = 'Customer';
            }
            $name = $record->name;
            $email = $record->email;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "is_admin" => $user,
                "name" => $name,
                "email" => $email,
                "contact"=>$record->contact,
                "is_active" => $status,
                'action' => "<a class='edit_user btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('users.edit', $id) . "'>Edit</a>  &nbsp;&nbsp;<a class='delete_user btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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
        $this->_hasMenuAccess('UserManagement-User');
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|unique:users,email|max:255',
                'contact_number' => 'required|unique:users,contact|digits:10',
                'password' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                $lastSixDigits = substr($request->contact_number, -6);

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->contact = $request->contact_number;
                $user->password = bcrypt($request->password);
                $user->mpin = $lastSixDigits;
                $user->is_active =true;
                $user->is_admin =true;
                $user->is_verified =true;
                $user->save();

                $output = array(
                    'response' => True,
                    'msg' => "User Created Successfully",
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
        $this->_hasMenuAccess('UserManagement-User');

        $user = User::where('id', $id)->first();
        return view('admin.users.edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $this->_hasMenuAccess('UserManagement-User');

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|max:255|unique:users,email,' . $id,
                'contact_number' => 'required|digits:10|unique:users,contact,' . $id,
                'password' => 'max:255'
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            DB::beginTransaction();
            $lastSixDigits = substr($request->contact_number, -6);

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->contact = $request->contact_number;
            $user->mpin = $lastSixDigits;
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            $user->is_verified =true;
            $user->is_active =true;
            $user->save();
            DB::commit();
            $output['response'] = true;
            $output['msg'] = 'user updated successfully';

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    function deleteUser(Request $request)
    {

        $this->_hasMenuAccess('UserManagement-User');

        User::where('id', $request->userId)->delete();

        $output = array(
            'response' => True,
            'msg' => "User  Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    public function add_customer_address(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $validator = Validator::make($request->all(), [
                'province_id'=>['required','numeric'],
                'district_id'=>['required','numeric'],
                'address'=>['required','string']
            ]);

            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            if(empty($request->customer_address_id)){
                throw new \Exception("choose customer first");
            }

           $address =  Address::create([
                'user_id'=>$request->post('customer_address_id'),
                'province_id'=>$request->post('province_id'),
                'district_id'=>$request->post('district_id'),
                'street'=>$request->post('address')
            ]);
            $userAddress = getProviceName($address->province_id).'-'.getDistrictName($address->district_id) .','.$address->street;

            $arrayData = [
                'user_address'=>$userAddress,
                'address_id'=>$address->id
            ];
            $output['response'] = true;
            $output['msg'] = 'address added successfully';
            $output['result'] = $arrayData;

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function getCustomerAddresses(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $address = Address::where('user_id',$request->user_id)->get();
            $arrayData = [];

            foreach ($address as $val){
                $arrayData[] = [
                    'user_address' => getProviceName($val->province_id) . '-'. getDistrictName($val->district_id) . ',' . $val->street,
                    'address_id'=>$val->id
                ];

            }

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $arrayData;

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }
}