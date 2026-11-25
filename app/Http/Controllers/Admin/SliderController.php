<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;

use App\Models\Slider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

use Exception;
use Illuminate\Support\Facades\Response;

class SliderController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('HomePageSetup-SliderSetup');

        return view('admin.sliders.index');
    }

    public function getSliders(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-SliderSetup');

        $length = $request->post('length');
        $start = $request->post('start');
        $columns = $request->post('columns');
        $order = $request->post('order');

        $orderBySql = "ORDER BY " . $columns[$order[0]['column']]['data'] . " " . $order[0]['dir'];
        $whereAllSql = "WHERE 1 ";
        foreach ($columns as $key => $value) {
            if (!empty($request->post('search')['value']) && 'action' != $value['data']) {
                $init = (0 == $key) ? 'AND ' : 'OR ';

                $sqlString = $init . $value['data'] . " LIKE '%" . $request->post('search')['value'] . "%' ";
                $whereAllSql .= $sqlString;
            }
        }

        $basicQuery = Slider::select(DB::raw('CASE WHEN sliders.is_active = 1 THEN "Active" ELSE "Not-Active" END AS status, sliders.id, sliders.main_title, categories.name AS category_name'))
            ->leftJoin('categories', 'categories.id', '=', 'sliders.category_id')
            ->toSql();

        if ($length == -1) {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql";
        } else {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql LIMIT $start, $length";
        }

        $queryResult = DB::select($dataQuery);

        $result = array();
        foreach ($queryResult as $value) {
            $result[] = array(
                "main_title" => $value->main_title,
                "category_name" => $value->category_name,
                "status" => $value->status,
                "action" => "<a class='edit_slider btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('sliders.edit', $value->id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_slider btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $value->id . "' href='javascript:'>Delete</a>"
            );
        }

        $recordsFilteredQuery = "SELECT count(id) AS records_filtered FROM ($basicQuery) a $whereAllSql";
        $recordsFiltered = DB::select($recordsFilteredQuery);
        $recordsFiltered = $recordsFiltered[0]->records_filtered;

        $response = array(
            'draw' => intval($request->post('draw')) + 1,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        );

        print(json_encode($response));
        exit();
    }

    public function store(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-SliderSetup');

        try {

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|numeric',
                'main_title' => 'required|unique:sliders,main_title|max:255',
                'title' => 'max:255',
                'image' => 'required|mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                $slider = new Slider();

                if ($request->file('image')) {

                    $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

                    $request->file('image')->move(public_path('/uploads/sliders'), $imageName);
                    $slider->image = $imageName;
                }

                if ($request->file('mobile_image')) {
                    $mobileImage = date('m-d-Y_H-i-s') . $request->file('mobile_image')->getClientOriginalName();
                    $request->file('mobile_image')->move(public_path('/uploads/sliders'), $mobileImage);
                    $slider->mobile_image = $mobileImage;
                }

                $slider->main_title = $request->main_title;
                $slider->title = $request->title;
                $slider->category_id = $request->category_id;
                $slider->is_active = 1;
                $slider->save();

                $output = array(
                    'response' => True,
                    'msg' => "Slider Image Created Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function edit($id)
    {
        $this->_hasMenuAccess('HomePageSetup-SliderSetup');
        $slider = Slider::where('id', $id)->first();
        $categories = Category::where('parent_id',0)->get();
        return view('admin.sliders.edit', compact('slider','categories'));
    }


    public function update(Request $request, $id)
    {
        $this->_hasMenuAccess('HomePageSetup-SliderSetup');
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'main_title' => 'required|max:255|unique:sliders,main_title,' . $id,
                'title' => 'max:255',
                'image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                $slider = Slider::find($id);
                $slider->main_title = $request->main_title;
                $slider->category_id = $request->category_id;
                $slider->title = $request->title;
                $slider->is_active = 1;

                if ($request->file('image')) {
                    File::delete(public_path('/uploads/sliders/' . $slider->image));
                    $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                    $request->file('image')->move(public_path('/uploads/sliders'), $imageName);
                    $slider->image = $imageName;
                }

                if ($request->file('mobile_image')) {
                    File::delete(public_path('/uploads/sliders/' . $slider->mobile_image));
                    $mobileImage = date('m-d-Y_H-i-s') . $request->file('mobile_image')->getClientOriginalName();

                    $request->file('mobile_image')->move(public_path('/uploads/sliders'), $mobileImage);
                    $slider->mobile_image = $mobileImage;
                }

                $slider->save();

                $output = array(
                    'response' => True,
                    'msg' => "Slider Image Updated Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    function deleteSliders(Request $request)
    {

        $this->_hasMenuAccess('HomePageSetup-SliderSetup');

        $slider = Slider::where('id', $request->sliderId)->first();
        File::delete(public_path('/uploads/sliders/' . $slider->image));
        $slider->delete();

        $output = array(
            'response' => True,
            'msg' => "Slider Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }
}
