<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponProduct;
use App\Models\CouponCategory;
use App\Models\Product;
use App\Models\Category;


use Exception;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller{

  public function index(){
    $this->_hasMenuAccess('CouponManagement-Index');

    $products = Product::where('is_active', 1)->get();
    $categories = Category::where('parent_id', 0)
    ->with('children')
    ->get();

    return view('admin.coupons.index', compact('products', 'categories'));
  }

  public function getCoupons(Request $request){
    $this->_hasMenuAccess('CouponManagement-Index');

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
    $totalRecords = Coupon::select('count(*) as allcount')->count();
    $totalRecordswithFilter = Coupon::select('count(*) as allcount')->where('code', 'like', '%' . $searchValue . '%')->count();

    /*Fetch records*/
    if ($request->length == -1) {
      $records = Coupon::orderBy($columnName, $columnSortOrder)
      ->where('coupons.code', 'like', '%' . $searchValue . '%')
      ->select('id', 'code', 'value', 'is_active')
      ->take($rowperpage)
      ->get();
    } else {
      $records = Coupon::orderBy($columnName, $columnSortOrder)
      ->where('coupons.code', 'like', '%' . $searchValue . '%')
      ->select('id', 'code', 'value', 'is_active')
      ->skip($start)
      ->take($rowperpage)
      ->get();
    }

    $data_arr = array();

    $i = 1;
    foreach ($records as $record) {
      $id = $record->id;
      $code = $record->code;
      $value = $record->value;
      $is_active = $record->is_active;

      if ($is_active == 1) {
        $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
      } else {
        $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
      }

      $data_arr[] = array(
        "id" => $i,
        "code" => $code,
        "value" => $value,
        "is_active" => $status,
        'action' => "<a class='edit_coupon btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('coupons.edit', $id) . "'>Edit</a>"
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

  public function store(Request $request){
    $this->_hasMenuAccess('CouponManagement-Index');

    $validator = Validator::make($request->all(), [
      'code' => 'required|unique:coupons,code|max:255',
      'is_percent' => 'required',
      'value' => 'required',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'minimum_spend' => 'numeric',
      'maximum_spend' => 'numeric',
      'usage_limit_of_person' => 'numeric',
      'usage_limit_of_coupon' => 'numeric',
    ]);

    if ($validator->fails()) {
      return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
    }
    else {

      $productLength = 0;
      if ($request->products) {
        $products = $request->products;
        $productLength = count($products);
      }

      $categoryLength = 0;
      if($request->categories){
        $categories = $request->categories;
        $categoryLength = count($categories);
      }

      $coupon = new Coupon();
      $coupon->code = $request->code;
      $coupon->is_percent = $request->is_percent;
      $coupon->value = $request->value;
      $coupon->minimum_spend = $request->minimum_spend;
      $coupon->maximum_spend = $request->maximum_spend;
      $coupon->usage_limit_of_coupon = $request->usage_limit_of_coupon;
      $coupon->usage_limit_of_person = $request->usage_limit_of_person;
      $coupon->start_date = $request->start_date;
      $coupon->end_date = $request->end_date;
      $coupon->is_active = ($request->is_active) ? 1 : 0;
      $coupon->save();

      if ($coupon->id) {
        $couponId = $coupon->id;

        for ($i = 0; $i < $productLength; $i++) {
          $cp = new CouponProduct();
          $cp->coupon_id = $couponId;
          $cp->product_id = $products[$i];
          $cp->save();
        }

        for ($i = 0; $i < $categoryLength; $i++) {
          $cp = new CouponCategory();
          $cp->coupon_id = $couponId;
          $cp->category_id = $categories[$i];
          $cp->save();
        }
      }

      return response()->json(['status' => 1, 'msg' => "Coupon Created Successfully"]);
    }
  }

  public function edit($id){
    $this->_hasMenuAccess('CouponManagement-Index');

    $products = Product::where('is_active', 1)->get();
    $categories = Category::where('parent_id', 0)
    ->with('children')
    ->get();

    $coupon = Coupon::where('id', $id)->first();

    if ($coupon) {
      $couponProducts = CouponProducts($coupon->id);
      $couponCategories = CouponCategories($coupon->id);
    } else {
      $couponProducts = null;
      $couponCategories = null;
    }

    return view('admin.coupons.edit', compact('products', 'categories', 'coupon', 'couponProducts', 'couponCategories'));
  }

  public function update(Request $request, $id){
    $this->_hasMenuAccess('CouponManagement-Index');

    $output = array(
      'response' => FALSE,
      'msg' => "Something went wrong. Please try again later.",
      'result' => "",
    );

    try{

      $validator = Validator::make($request->all(), [
        'code' => 'required|max:255|unique:coupons,code,' . $id,
        'is_percent' => 'required',
        'value' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'minimum_spend' => 'numeric',
        'maximum_spend' => 'numeric',
        'usage_limit_of_person' => 'numeric',
        'usage_limit_of_coupon' => 'numeric',
      ]);

      if ($validator->fails()) {
        $output = array(
          'result' => $validator->errors()->toArray(),
        );

        throw new Exception("Form Validation Error");
      }
      else {

        if (empty($request->products) && empty($request->categories)) {
          throw new Exception("Product and Category Both Cannot Be Empty.");
        }

        $productLength = 0;
        $products = $request->products;
        if (!empty($request->products)) {
          $productLength = count($products);
        }

        $categoryLength = 0;
        $categories = $request->categories;
        if (!empty($request->categories)) {
          $categoryLength = count($categories);
        }

        $coupon = Coupon::find($id);
        $coupon->code = $request->code;
        $coupon->is_percent = $request->is_percent;
        $coupon->value = $request->value;
        $coupon->minimum_spend = $request->minimum_spend;
        $coupon->maximum_spend = $request->maximum_spend;
        $coupon->usage_limit_of_coupon = $request->usage_limit_of_coupon;
        $coupon->usage_limit_of_person = $request->usage_limit_of_person;
        $coupon->start_date = $request->start_date;
        $coupon->end_date = $request->end_date;
        $coupon->is_active = ($request->is_active == 'on') ? 1 : 0;
        $coupon->save();

        $couponId = $coupon->id;

        if ($couponId) {
          CouponProduct::where('coupon_id', $couponId)->delete();
          CouponCategory::where('coupon_id', $couponId)->delete();

          for ($i = 0; $i < $productLength; $i++) {
            $cp = new CouponProduct();
            $cp->coupon_id = $couponId;
            $cp->product_id = $products[$i];
            $cp->save();
          }

          for ($i = 0; $i < $categoryLength; $i++) {
            $cp = new CouponCategory();
            $cp->coupon_id = $couponId;
            $cp->category_id = $categories[$i];
            $cp->save();
          }
        }

        $output = array(
          'response' => True,
          'msg' => "Coupon Updated Successfully",
          'result' => "",
        );
      }
    } catch (Exception $e) {
      $output['response'] = FALSE;
      $output['msg'] = $e->getMessage();
    }

    return Response::json($output);
  }
}
