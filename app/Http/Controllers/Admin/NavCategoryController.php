<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NavCategoryController extends Controller
{
    public function index()
    {
        $this->_hasMenuAccess('nav.category.index');

        $data = NavCategory::first();
        $categories = Category::where('parent_id', 0)->where('is_active', true)->get();

        return view('admin.NavCategory.index', compact('categories', 'data'));
    }

    public function store(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'category_1' => 'required|integer',
                'category_2' => 'required|integer',
                'category_3' => 'required|integer',
                'category_4' => 'required|integer',
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                $nav = NavCategory::first();
                $nav->category_1 = $request->category_1;
                $nav->category_2 = $request->category_2;
                $nav->category_3 = $request->category_3;
                $nav->category_4 = $request->category_4;
                $nav->created_by = auth()->id();
                $nav->created_date = now();
                $nav->save();
                $output = array(
                    'response' => true,
                    'msg' => "Save Successfully",
                    'result' => "",
                );
            }
        } catch (Exception  $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }
}
