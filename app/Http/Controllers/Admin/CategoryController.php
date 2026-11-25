<?php

namespace App\Http\Controllers\Admin;


use App\Models\NavCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        return view('admin.categories.index');
    }

    public function getCategoriesList()
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $data = Category::orderBy('parent_id', 'asc')
                ->where('is_active', 1)
                ->select('id', 'name', 'slug', 'parent_id', 'image', 'is_active')
                ->get();

            $menuData = json_decode(json_encode($data), true);

            $result = array();
            foreach ($menuData as $key => $value) {
                $value['enc_id'] = urlencode(base64_encode($value['id']));
                $result[$value['id']] = $value;
            }

            $dataOutput = '';
            $dataOutput = arrayHierarchy($result, 'parent_id', 'id');

            $output = array(
                'result' => $dataOutput,
                'response' => TRUE,
                'msg' => '',
            );
        } catch (Exception $e) {
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    function getEditCategoryForm(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        $encCategoryId = $request->get('id');
        $id = base64_decode(urldecode($encCategoryId));

        $category = Category::where('id', $id)->first();

        $output = array(
            'result' => view('admin.categories.edit', compact('category'))->render(),
            'response' => TRUE,
            'msg' => '',
        );

        return Response::json($output);
    }

    function editCategory(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $id = $request->id;

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:categories,name,' . $id,
                'slug' => 'required|max:255|unique:categories,slug,' . $id,
                'parent_category' => 'required',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {

                $category = Category::find($id);
                $category->parent_id = (empty($request->parent_category)) ? 0 : base64_decode(urldecode($request->parent_category));
                $category->name = $request->name;
                $category->slug = $request->slug;
                $category->ordering = $request->order;
                $category->show_in_home = boolval( $request->show_in_home);
                $category->is_active = 1;

                if ($request->file('image')) {
                    if (File::exists(public_path('/uploads/categories/' . $category->image))) {
                        File::delete(public_path('/uploads/categories/' . $category->image));
                    }
                    $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                    $request->file('image')->move(public_path('/uploads/categories'), $imageName);
                    $category->image = $imageName;
                }

                if ($request->file('mobile_image')) {
                    if (File::exists(public_path('/uploads/categories/' . $category->mobile_image))) {
                        File::delete(public_path('/uploads/categories/' . $category->mobile_image));
                    }
                    $mobileImage = date('m-d-Y_H-i-s') . $request->file('mobile_image')->getClientOriginalName();
                    $request->file('mobile_image')->move(public_path('/uploads/categories'), $mobileImage);
                    $category->mobile_image = $mobileImage;
                }

                $category->save();

                $output = array(
                    'response' => True,
                    'msg' => "Category Updated Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            if ($e->getMessage() !== 'Form Validation Error') {
                Log::error('Category update failed: ' . $e->getMessage(), ['exception' => $e]);
            }
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    function getAddCategoryForm()
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        $output = array(
            'result' => view('admin.categories.add', ['nextOrder' => ((int) Category::max('ordering')) + 1])->render(),
            'response' => TRUE,
            'msg' => '',
        );

        return Response::json($output);
    }

    public function addCategory(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Category');

        $output = array(
            'response' => FALSE,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:categories,name|max:255',
            ]);

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            } else {
                DB::beginTransaction();
                $category = new Category();

                if ($request->file('image')) {
                    $imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();
                    $request->file('image')->move(public_path('/uploads/categories'), $imageName);
                    $category->image = $imageName;
                }

                if ($request->file('mobile_image')) {
                    $mobileImage = date('m-d-Y_H-i-s') . $request->file('mobile_image')->getClientOriginalName();
                    $request->file('mobile_image')->move(public_path('/uploads/categories'), $mobileImage);
                    $category->mobile_image = $mobileImage;
                }

                $category->parent_id = (empty($request->parent_category)) ? 0 : base64_decode(urldecode($request->parent_category));
                $category->name = $request->name;
                $category->ordering = ($request->order === null || $request->order === '') ? ((int) Category::max('ordering')) + 1 : $request->order;
                $category->show_in_home = boolval( $request->show_in_home);
                $category->slug = strtolower(preg_replace('/\s+/', '_', $request->name));
                $category->is_active = 1;
                $category->save();

                DB::commit();
                $output = array(
                    'response' => True,
                    'msg' => "Category Created Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            if ($e->getMessage() !== 'Form Validation Error') {
                Log::error('Category create failed: ' . $e->getMessage(), ['exception' => $e]);
            }
            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function deleteCategory(Request $request)
    {

        $this->_hasMenuAccess('ProductSetup-Category');

        $encCategoryId = $request->get('id');
        $id = base64_decode(urldecode($encCategoryId));

        Category::where('id', $id)->update([
            'is_active' => 0
        ]);

        //remove from nav_category if category deleted

        $cat1 =  NavCategory::where([
            ['category_1',$id],
        ])->first();

        $cat2 =  NavCategory::where([
            ['category_2',$id],
        ])->first();

        $cat3 =  NavCategory::where([
            ['category_3',$id],
        ])->first();

        $cat4 =  NavCategory::where([
            ['category_4',$id],
        ])->first();

        if($cat1){
            NavCategory::where([
                ['category_1',$id],
            ])->update([
                'category_1' =>null
            ]);
        }

        elseif ($cat2){
            NavCategory::where([
                ['category_2',$id],
            ])->update([
                'category_2' =>null
            ]);
        }

        elseif ($cat3){
            NavCategory::where([
                ['category_3',$id],
            ])->update([
                'category_3' =>null
            ]);
        }

        else{
            NavCategory::where([
                ['category_4',$id],
            ])->update([
                'category_4' =>null
            ]);
        }
        $output = array(
            'response' => True,
            'msg' => "Category   Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    public function search(Request $request): JsonResponse
    {
        $data = Category::select('id', 'name')
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

    public function viewCategory(request $request){
        $encCategoryId = $request->get('categoryId');
        $categoryId = base64_decode(urldecode($encCategoryId));

        $category = DB::select(" SELECT a.name, a.slug, b.name AS parent_category, a.image,a.mobile_image FROM categories a
      LEFT JOIN categories b ON b.id = a.parent_id
      WHERE a.id = '$categoryId' ");

        $output = array(
            'result' => view('admin.categories.view', compact('category'))->render(),
            'response' => TRUE,
            'msg' => '',
        );
        return Response::json($output);
    }

}
