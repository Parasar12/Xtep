<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMaster;
use App\ApiResponse\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use stdClass;

class CategoryApiController extends Controller{

  public function getAllCategories(){

    $categories = Category::select('id', 'parent_id', 'name','mobile_image')
    ->where('is_active', 1)->get();
    $path = url('uploads/categories/');

    $categoryArray = json_decode(json_encode($categories), true);
    $result = array();
    foreach ($categoryArray as $key => $value) {
        $result[] = [
            'id'=>$value['id'],
            'parent_id'=>$value['parent_id'],
            'name'=>$value['name'],
            'image'=>isset($value['mobile_image']) ? $path.'/'.$value['mobile_image'] : $path.'/'
        ];
//      if(isset($value['mobile_image'])) {
//        $value['image'] = $path.'/'.$value['mobile_image'];
//      }
//      $result[$value['id']] = $value;
    }
    $categoryData = arrayHierarchy($result, 'parent_id', 'id');
    return ApiResponse::sendSuccessResponse('Success',$categoryData, $path);
  }

  public function getParentCategoryList(){
    $categories = Category::select('id', 'name', 'mobile_image')
    ->where('is_active', 1)
    ->where('parent_id', 0)
    ->get();
//    return $categories;
    $path = url('uploads/categories/');

    $categoryArray = json_decode(json_encode($categories), true);

    $result = array();
    foreach ($categoryArray as $key => $value) {
//      if(isset($value['mobile_image'])) {
//        $value['image'] = $path.'/'.$value['mobile_image'];
//      }
      $result[] = [
          'id'=>$value['id'],
          'name'=>$value['name'],
          'image' => isset($value['mobile_image']) ? $path.'/'.$value['mobile_image'] : $path.'/'
      ];
    }

    return ApiResponse::sendSuccessResponse('Success',$result, $path);
  }

  public function getCategoryProducts(Request $request){
    $rules = array(
      'category_id' => 'required|numeric',
      'limit' => 'required|numeric',
      'offset' => 'required|numeric',
    );

    $messages = array(
      'category_id.required' => 'Category Id is required.',
      'limit.required' => 'Limit is required.',
      'offset.required' => 'Offset is required.',
    );
    


    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $messages = $validator->messages();
      $errors = $messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }

    $categoryCheck = Category::where('id', $request->category_id)
    ->where('is_active', 1)->first();

    if($categoryCheck){

      $categoryArray = getCategoryProducts($categoryCheck->slug);

      if(empty($categoryArray)) {
        return ApiResponse::sendNoDataErrorResponse('No Data found.');
      }
      else{

        $offset = ($request->offset==0) ? 0 : ($request->offset * $request->limit);

        $catgoryArrayList = array_slice($categoryArray, $offset, $request->limit);

        if (!empty($catgoryArrayList)) {
          foreach ($catgoryArrayList as $key => $value) {
            $product[] = getProductDetail($value);
          }

          return ApiResponse::sendSuccessResponse('Success', $product);
        }
        else{
          return ApiResponse::sendNoDataErrorResponse('No Data found.');
        }
      }
    }
    else{
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    }
  }

}
