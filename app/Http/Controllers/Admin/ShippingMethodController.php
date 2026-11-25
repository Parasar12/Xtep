<?php

namespace App\Http\Controllers\Admin;
;

use Illuminate\Http\Request;
use App\Models\ShippingMethod;

use Illuminate\Support\Facades\Validator;

class ShippingMethodController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('MasterSetup-ShippingMethod');

        return view('admin.shippings.index');
    }

    public function getShippings(Request $request)
    {
        $this->_hasMenuAccess('MasterSetup-ShippingMethod');

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
        $totalRecords = ShippingMethod::select('count(*) as allcount')->count();
        $totalRecordswithFilter = ShippingMethod::select('count(*) as allcount')->where('title', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = ShippingMethod::orderBy($columnName, $columnSortOrder)
                ->where('shipping_methods.title', 'like', '%' . $searchValue . '%')
                ->select('id', 'title', 'duration', 'cost', 'is_active')
                ->take($rowperpage)
                ->get();
        } else {
            $records = ShippingMethod::orderBy($columnName, $columnSortOrder)
                ->where('shipping_methods.title', 'like', '%' . $searchValue . '%')
                ->select('id', 'title', 'duration', 'cost', 'is_active')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $title = $record->title;
            $duration = $record->duration;
            $cost = $record->cost;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "title" => $title,
                "duration" => $duration,
                "cost" => $cost,
                "is_active" => $status,
                'action' => "<a class='edit_shipping btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('shippings.edit', $id) . "'>Edit</a>"
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

    public function create()
    {

    }

    public function store(Request $request)
    {
        $this->_hasMenuAccess('MasterSetup-ShippingMethod');

        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:shipping_methods,title|max:255',
            'duration' => 'required|string|max:255',
            'cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $shipping = new ShippingMethod();

            $shipping->title = $request->title;
            $shipping->duration = $request->duration;
            $shipping->cost = $request->cost;

            if ($request->is_active == 'on') {
                $shipping->is_active = 1;
            } else {
                $shipping->is_active = 0;
            }
            $shipping->save();

            return response()->json(['status' => 1, 'msg' => "Shipping Method Created Successfully"]);
        }
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $this->_hasMenuAccess('MasterSetup-ShippingMethod');

        $shipping = ShippingMethod::where('id', $id)->first();
        return view('admin.shippings.edit', compact('shipping'));
    }

    public function update(Request $request, $id)
    {
        $this->_hasMenuAccess('MasterSetup-ShippingMethod');

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255|unique:shipping_methods,title,' . $id,
            'duration' => 'required|string|max:255',
            'cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {

            $shipping = ShippingMethod::find($id);

            $shipping->title = $request->title;
            $shipping->duration = $request->duration;
            $shipping->cost = $request->cost;

            if ($request->is_active == 'on') {
                $shipping->is_active = 1;
            } else {
                $shipping->is_active = 0;
            }
            $shipping->save();

            return response()->json(['status' => 1, 'msg' => "Shipping Method Updated Successfully"]);
        }
    }

    public function destroy($id)
    {

    }

}
