<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\ApiResponse\ApiResponse;

use App\Models\Brand;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

use Image;

class BrandController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        return view('admin.brands.index');
    }

    public function getBrands(Request $request)
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
        $totalRecords = Brand::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Brand::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Brand::orderBy($columnName, $columnSortOrder)
                ->where('brands.name', 'like', '%' . $searchValue . '%')
                ->select('id', 'name', 'slug', 'is_active')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Brand::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['brands.name', 'like', '%' . $searchValue . '%'],
                    ['is_active', 1],
                ])
                ->select('id', 'name', 'slug', 'is_active')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            $slug = $record->slug;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "slug" => $slug,
                "is_active" => $status,
                'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('brands.edit', $id) . "'>Edit</a>  &nbsp;&nbsp;<a class='delete_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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
                'name' => 'required|unique:brands,name|max:255',
            ]);
    
            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );
                throw new Exception("Form Validation Error");
            } else {
    
                $brand = new Brand();

                if ($request->file('image')) {
                    $extension = $request->image->extension();
                    $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
    
                    $request->file('image')->move(public_path('/uploads/brands'), $imageName);
                    $brand->image = $imageName;
                }
    
                $brand->name = $request->name;
                $brand->slug = strtolower(preg_replace('/\s+/', '_', $request->name));
                $brand->is_active = 1;
                $brand->save();
    
                $output = array(
                    'response' => True,
                    'msg' => "Brand Created Successfully",
                    'result' => "",
                );
            }

        }catch(Exception $e){
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        $brand = Brand::where('id', $id)->first();
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Brand');

        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|unique:brands,name,' . $id,
            'slug' => 'required|max:255|unique:brands,slug,' . $id,
            'image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {

            $brand = Brand::find($id);
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->is_active = 1;

            if ($request->file('image')) {
                $extension = $request->image->extension();
                $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

                $request->file('image')->move(public_path('/uploads/brands'), $imageName);
                $brand->image = $imageName;
            }

            $brand->save();

            return response()->json(['status' => 1, 'msg' => "Brand Updated Successfully"]);
        }
    }

    function deleteBrands(Request $request)
    {

        $this->_hasMenuAccess('ProductSetup-Brand');

        Brand::where('id', $request->brandId)->update([
            'is_active' => 0
        ]);

        $output = array(
            'response' => True,
            'msg' => "Brand  Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    public function search(Request $request): JsonResponse
    {
        $data = Brand::select('id', 'name')
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
