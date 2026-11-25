<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

use App\Models\CategoryBanner;
use App\Models\Category;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CategoryBannerController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

        $banners = CategoryBanner::where('is_active', 1)->get();

        return view('admin.banner.index', compact('banners'));
    }

    public function edit($id)
    {
        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

        $banner = CategoryBanner::where('id', $id)->first();
        $c = Category::where('id', $banner->category_id)->first();
        return view('admin.banner.edit', compact('banner', 'c'));
    }

    public function getBanners(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

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
        $totalRecords = CategoryBanner::select('count(*) as allcount')->count();
        $totalRecordswithFilter = CategoryBanner::select('count(*) as allcount')->where('heading', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = CategoryBanner::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['category_banners.heading', 'like', '%' . $searchValue . '%'],
                    ['is_active', 1],
                ])
                ->select('id', 'heading', 'title', 'is_active')
                ->take($rowperpage)
                ->get();
        } else {
            $records = CategoryBanner::orderBy($columnName, $columnSortOrder)
                ->where([
                    ['category_banners.heading', 'like', '%' . $searchValue . '%'],
                    ['is_active', 1],
                ])
                ->select('id', 'heading', 'title', 'is_active')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $heading = $record->heading;
            $title = $record->title;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "heading" => $heading,
                "title" => $title,
                "is_active" => $status,
                'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('banners.edit', $id) . "'>Edit</a> &nbsp;&nbsp;<a class='delete_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $id . "' href='javascript:'>Delete</a>"
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
        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|unique:category_banners,category_id|numeric',
            'heading' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'image' => 'required|mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $banner = new CategoryBanner();
            $banner->category_id = $request->category_id;
            $banner->heading = $request->heading;
            $banner->title = $request->title;
            $banner->sub_title = $request->sub_title;
            $banner->is_active = 1;

            if ($request->file('image')) {
                $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('/uploads/categories/banners'), $imageName);
                $banner->banner_image = $imageName;
            }

            $banner->save();
            return response()->json(['status' => 1, 'msg' => "Category Banner Created Successfully."]);
        }
    }

    public function update(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

        $id = $request->id;
        $categoryId = $request->categoryId;
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|unique:category_banners,category_id,' . $id,
            'heading' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'sub_title' => 'required|string|max:255',
            'image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {
            $banner = CategoryBanner::find($id);
            $banner->category_id = $request->category_id;
            $banner->heading = $request->heading;
            $banner->title = $request->title;
            $banner->sub_title = $request->sub_title;
            $banner->is_active = 1;

            if ($request->file('image')) {

                $extension = $request->image->extension();
                $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

                $request->file('image')->move(public_path('/uploads/categories/banners'), $imageName);
                $banner->banner_image = $imageName;
            }

            $banner->save();
            return response()->json(['status' => 1, 'msg' => "Category Banner Updated Successfully."]);
        }
    }

    function deleteBanners(Request $request)
    {

        $this->_hasMenuAccess('HomePageSetup-ProductCategoryBanner');

        CategoryBanner::where('id', $request->bannerId)->delete();

        $output = array(
            'response' => True,
            'msg' => "Category  Banner Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }
}
