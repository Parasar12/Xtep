<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserPasswordSend;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductMaster;
use App\Models\QuickOrder;
use App\Models\UserTokenModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\Province;
use App\Models\District;
use App\Models\Address;
use App\Models\OrderLog;
use App\Models\Coupon;

use App\Models\User;

use App\Models\ShippingMethod;


use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Cart;

use stdClass;
use Exception;


class OrderApiController extends Controller
{

    public function getUserOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)->get();

        if (!$orders->isEmpty()) {

            $orderInfo = [];
            foreach ($orders as $key => $value) {
                $billingAddress = DB::table('addresses')
                    ->join('zone_state_master', 'zone_state_master.zone_state_id', '=', 'addresses.province_id', 'LEFT')
                    ->join('district_master', 'district_master.district_id', '=', 'addresses.district_id', 'LEFT')
                    ->where('addresses.user_id', auth()->user()->id)
                    ->where('addresses.id', $value->billing_address_id)
                    ->select('addresses.id', 'zone_state_master.zone_state_name', 'district_master.district_name', 'addresses.street')
                    ->first();

                $shippingAddress = DB::table('addresses')
                    ->join('zone_state_master', 'zone_state_master.zone_state_id', '=', 'addresses.province_id', 'LEFT')
                    ->join('district_master', 'district_master.district_id', '=', 'addresses.district_id', 'LEFT')
                    ->where('addresses.user_id', auth()->user()->id)
                    ->where('addresses.id', $value->shipping_address_id)
                    ->select('addresses.id', 'zone_state_master.zone_state_name', 'district_master.district_name', 'addresses.street')
                    ->first();

                $paymentType = DB::table('payment_mode')
                    ->where('payment_mode_id', $value->payment_type)
                    ->first();

                $detail = OrderDetail::where('order_id', $value->id)->get();

                $detailArray = [];
                $netPrice = 0;
                foreach ($detail as $key => $data) {

                    $attributeData = '';

                    if (!empty($data->attribute)) {

                        $attributeData = $data->attribute;
//            $attributeList = json_decode($data->attribute, TRUE);
//
//            foreach ($attributeList as $key => $val) {
//              $attributeData[] = array(
//                'name' => \App\Models\Attribute::find($val['attribute_id'])->name,
//                'value' => $val['value'],
//              );
//            }
                    }

                    $paymentStatus = DB::table('order_status')
                        ->where('id', $data->payment_status)
                        ->first();

                    $deliveryStatus = DB::table('order_status')
                        ->where('id', $data->delivery_status)
                        ->first();

                    $detailArray[] = array(
                        'code' => $data->code,
                        'product_name' => $data->product->name,
                        'sku' => $data->product->sku,
                        'image' => getProductGalleryImage($data->product_id),

                        'quantity' => $data->quantity,
                        'price' => $data->price,

                        'total' => $data->price * $data->quantity,
                        'coupon_discount' => ($data->product_coupon_discount) ? $data->product_coupon_discount : 0,

                        'payment_status' => $data->payment_status,
                        'payment_status_name' => $paymentStatus->name,
                        'delivery_status' => $data->delivery_status,
                        'delivery_status_name' => $deliveryStatus->name,
                        'attribute_flag' => (empty($data->attribute)) ? 0 : 1,
                        'attribute' => $attributeData,
                    );

                    $netPrice += ($data->price * $data->quantity);
                }

                $duration = $value->shippingMethod->duration_date;

                $orderInfo[] = array(
                    'code' => $value->code,
                    'billing_address' => $billingAddress->street . ', ' . $billingAddress->zone_state_name . ', ' . $billingAddress->district_name,
                    'shipping_address' => $shippingAddress->street . ', ' . $shippingAddress->zone_state_name . ', ' . $shippingAddress->district_name,

                    'order_date' => \Carbon\Carbon::parse($value->date)->isoFormat('Do MMM YYYY'),
                    'shipping_duration' => $value->shippingMethod->duration,
                    'estimated_delivery_date' => date('jS F Y', strtotime($value->date . +$duration . 'day')),
                    'payment_type' => $paymentType->name,

                    'shipping_method' => $value->shippingMethod->title,

                    'grand_total' => $value->grand_total,
                    'shipping_cost' => $value->shippingMethod->cost,
                    'coupon_discount_total' => $value->total_coupon_discount,
                    'net_total' => $value->net_total,

                    'detail' => $detailArray,
                );
            }

            return ApiResponse::sendSuccessResponse('Success', $orderInfo);
        } else {
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }

    public function paymentMethods()
    {
        $data = DB::table('payment_mode')
            ->where('is_active', 1)
            ->orderBy('payment_mode_id', 'ASC')
            ->get();

        if (!$data->isEmpty()) {
            return ApiResponse::sendSuccessResponse('Success', $data);
        } else {
            return ApiResponse::sendNoDataErrorResponse('No Data is Available');
        }
    }

    public function checkCoupon(Request $request)
    {
        $rules = array(
            'code' => 'required',
            'cart' => 'required',
        );

        $messages = array(
            'code.required' => 'Coupon Code is required.',
            'cart.required' => 'Cart Details is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        } else {


            $today = Carbon::now()->toDateString();
            $totalCouponDiscount = 0;
            $sub_total = 0;
            $net_amount = 0;
            $data = [];
            $query = Coupon::where([
                ['code', $request->code],
                ['is_active', true]
            ]);
            $coupon = $query->first();
//        request cart data decoded to array from json decode
            $decoded_data = json_decode($request->cart);
            foreach ($decoded_data as $data) {
                $couponDiscountedPrice = 0;
                $productCouponDiscount = 0;

                $cartVal = Cart::where('id', $data)->first();

                if ($cartVal) {

                    $cart = $cartVal;
                } else {
                    $cart = QuickOrder::where('id', $data)->first();
                }

                $sub_total += $cart->price * $cart->quantity;
                if (!$coupon) {
                    $message = [
                        'you have entered wrong coupon code'
                    ];
                    return ApiResponse::sendValidationErrorResponse($message);
                } else {
                    $date_coupon = $query->where([
                        ['start_date', '<=', $today],
                        ['end_date', '>=', $today],
                    ])->first();

                    if ($date_coupon) {
                        $totalCouponUses = DB::table('order_coupon')->where('coupon_id', $date_coupon->id)->get()->count();
                        if ($totalCouponUses < $date_coupon->usage_limit_of_coupon) {

                            $couponOrderByUser = DB::table('order_coupon')->where([
                                ['user_id', \auth()->user()->id],
                                ['coupon_id', $date_coupon->id]
                            ])->get()->count();

                            if ($couponOrderByUser >= $coupon->usage_limit_of_person) {
                                $message = [
                                    'you have exceeded your coupon limit'
                                ];
                                return ApiResponse::sendValidationErrorResponse($message);
                            } else {
//                            coupon discount calculate here
                                $product = Product::where('id', $cart->product_id)->first();

                                $attributeData = [];

                                if (isset($cart->attribute)) {

                                    $product_attribute = ProductAttribute::find($cart->attribute);
                                    $attributeData [] = [
                                        'name' => \App\Models\Attribute::find($product_attribute->attribute_id)->name,
                                        'value' => $product_attribute->value,
                                    ];
                                }
//                            check if orderd product exists in product wise coupon discount

                                if (in_array($cart->product_id, getCouponProducts($coupon ? $coupon->id : ''))) {

                                    $couponPrice = getCouponDiscountedPrice($coupon->id, $product->id, $cart->quantity);
                                    $couponDiscountedPrice = $cart->quantity * $couponPrice;
                                    $productCouponDiscount = ($cart->price * $cart->quantity) - ($couponDiscountedPrice);
                                    $totalCouponDiscount += $productCouponDiscount;
                                }

//                            check if oredered product exists in category wise discount
                                if (in_array($cart->product_id, getCouponCategories($coupon ? $coupon->id : ''))) {
                                    $couponPrice = getCouponDiscountedPriceByCategory($coupon->id, $product->id, $cart->quantity);
                                    $couponDiscountedPrice = $couponPrice;
                                    $productCouponDiscount = ($cart->price * $cart->quantity) - ($couponDiscountedPrice);
                                    $totalCouponDiscount += $productCouponDiscount;
                                }

                                $discountedNewProduct[] = [
                                    'name' => $product->name,
                                    'price' => getProductPrice($product->id),
                                    'image' => asset('uploads/products/' . $product->image),
                                    'product_discount_price' => $productCouponDiscount,
                                    'attribute' => $attributeData
                                ];

                            }

                        } else {
                            $message = [
                                'coupon limit has been exceeded'
                            ];
                            return ApiResponse::sendValidationErrorResponse($message);
                        }
                    } else {
                        $message = [
                            'coupon has been expired'
                        ];
                        return ApiResponse::sendValidationErrorResponse($message);
                    }
                }

            };

        }

        if ($totalCouponDiscount == 0) {
            $message = [
                'no coupon discount applied to these products.'
            ];
            return ApiResponse::sendValidationErrorResponse($message);
        } else {
            $net_amount = $sub_total - $totalCouponDiscount;
            $data = [
                'sub_total' => $sub_total,
                'total_coupon_discount' => $totalCouponDiscount,
                'net_amount' => $net_amount,
                'product' => $discountedNewProduct
            ];
        }

        return ApiResponse::sendSuccessResponse('Coupon Applied Successfully', $data);
    }

    public function createOrder(Request $request)
    {
        $rules = array(
            'billing_address_id' => 'required|numeric',
            'shipping_address_id' => 'required|numeric',
            'payment_type' => 'required|numeric',
            'productDetail' => 'required',
            'coupon_discount' => 'required',
            'shipping_method_id' => 'required|numeric',
        );

        $messages = array(
            'billing_address_id.required' => 'Billing Address is required.',
            'shipping_address_id.required' => 'Shipping Address_id is required.',
            'payment_type.required' => 'Payment type is required.',
            'productDetail.required' => 'Product detail is required.',
            'coupon_discount.required' => 'Coupon Discount is required.',
            'shipping_method_id.required' => 'Shipping method is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        } else {

            DB::beginTransaction();

            try {
                $fiscal_year = getFiscalYear();
                $userInfo = User::select('is_verified')
                    ->where('id', auth()->user()->id)
                    ->where('type', 'normal')
                    ->where('is_active', 1)->where('is_admin', 0)->first();

                if (0 == $userInfo->is_verified) {
                    throw new Exception("User Not Verified");
                }

                $cartArray = ($request->productDetail);


                $lastOrderDetail = OrderDetail::orderBy('code', 'desc')->first();
                $code = ($lastOrderDetail) ? $lastOrderDetail->code + 1 : 100000;

                $grandPrice = 0;
                $totalCouponDiscount = $request->coupon_discount;
                foreach ($cartArray as $key => $value) {

                    $cart = Cart::select('product_id', 'price', 'quantity', 'attribute')
                        ->where('id', $value)
                        ->where('user_id', auth()->user()->id)
                        ->first();

                    if (!empty($cart)) {
                        $grandPrice += $cart->quantity * $cart->price;
                    } else {
                        return ApiResponse::sendNoDataErrorResponse('Invalid Cart Entry');
                    }
                }


                /*Shipping Cost*/
                $shippingCost = ShippingMethod::select('cost')
                    ->where('id', $request->shipping_method_id)->first();


                /*Order Setup*/
                $orderData = DB::table('orders')->max('id');
                $orderId = $orderData + 1;

                $order = new Order();
                $order->id = $orderId;
                $order->user_id = auth()->user()->id;

                $order->billing_address_id = $request->billing_address_id;
                $order->shipping_address_id = $request->shipping_address_id;

                $order->payment_type = $request->payment_type;
                $order->shipping_method_id = $request->shipping_method_id;
                $order->code = $code;
                $order->fiscal_year_id = $fiscal_year;
                $order->grand_total = $grandPrice;
                $order->total_coupon_discount = $totalCouponDiscount;
                $order->net_total = (($grandPrice + $shippingCost->cost) - $totalCouponDiscount);

                $order->save();


                /* Order Log*/
                $orderNotice = OrderStatus::select('msg')->where('id', 1)->first();

                $log = new OrderLog;
                $log->code = $code;
                $log->log = $orderNotice->msg;
                $log->save();


                /*Order Detail*/
                foreach ($cartArray as $key => $value) {

                    $cart = Cart::select('product_id', 'price', 'quantity', 'attribute')
                        ->where('id', $value)
                        ->where('user_id', auth()->user()->id)
                        ->first();

                    $details = new OrderDetail();
                    $details->order_id = $orderId;
                    $details->product_id = $cart->product_id;
                    $details->price = $cart->price;
                    $details->quantity = $cart->quantity;
                    $details->code = $code;
                    $details->product_coupon_discount = 0;
                    $details->delivery_status = 1;
                    $details->payment_status = 1;

                    if (!empty($cart->attribute)) {

//            $attributeArray = json_decode($cart->attribute, TRUE);

//            foreach ($attributeArray as $key => $val) {
//              $attribute[] = array(
//                'attribute_id' => $val['attribute_id'],
//                'value' => $val['value'],
//              );
//            }

                        $details->attribute = $cart->attribute;
                    }

                    $details->save();
                }

                foreach ($cartArray as $key => $value) {
                    DB::table('carts')->where('id', $value)->delete();
                }

                DB::commit();
                return ApiResponse::sendSuccessResponse('Order Created Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return ApiResponse::sendExceptionResponse($e->getMessage());
            }
        }
    }

    public function createQuickOrder(Request $request)
    {


        $rules = array(
            'billing_address_id' => 'required|numeric',
            'shipping_address_id' => 'required|numeric',
            'payment_type' => 'required|numeric',
            'product_id' => 'required',
            'attribute_id' => 'required',
            'qty' => 'required',
//      'coupon_discount' => 'required',
            'shipping_method_id' => 'required|numeric',
        );

        $messages = array(
            'billing_address_id.required' => 'Billing Address is required.',
            'shipping_address_id.required' => 'Shipping Address_id is required.',
            'payment_type.required' => 'Payment type is required.',
            'productDetail.required' => 'Product detail is required.',
            'coupon_discount.required' => 'Coupon Discount is required.',
            'shipping_method_id.required' => 'Shipping method is required.',
            'product_id.required' => 'Product is required.',
            'attribute_id.required' => 'Attribute  is required.',
            'qty.required' => 'Quantity  is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        } else {

            DB::beginTransaction();

            try {
                $userInfo = User::select('is_verified')
                    ->where('id', auth()->user()->id)
//                    ->where('type', 'normal')
                    ->where('is_active', 1)->where('is_admin', 0)->first();

                if (0 == $userInfo->is_verified) {
                    throw new Exception("User Not Verified");
                }

                $product = $request->product_id;
                $attribute = $request->attribute_id;
                $quantity = $request->qty;
                $cartArray = $request->cart_id;


                $lastOrderDetail = OrderDetail::orderBy('code', 'desc')->first();
                $code = ($lastOrderDetail) ? $lastOrderDetail->code + 1 : 100000;

                $grandPrice = 0;
                $totalCouponDiscount = $request->coupon_discount;


                /*Shipping Cost*/
                $shippingCost = ShippingMethod::select('cost')
                    ->where('id', $request->shipping_method_id)->first();


                /*Order Setup*/
                $orderData = DB::table('orders')->max('id');
                $orderId = $orderData + 1;

                $order = new Order();
                $order->id = $orderId;
                $order->user_id = auth()->user()->id;

                $order->billing_address_id = $request->billing_address_id;
                $order->shipping_address_id = $request->shipping_address_id;

                $order->payment_type = $request->payment_type;
                $order->shipping_method_id = $request->shipping_method_id;
                $order->code = $code;

                $order->grand_total = $request->grand_total;
                $order->total_coupon_discount = $totalCouponDiscount;
                $order->net_total = $request->net_price;

                $order->save();


                /* Order Log*/
                $orderNotice = OrderStatus::select('msg')->where('id', 1)->first();

                $log = new OrderLog;
                $log->code = $code;
                $log->log = $orderNotice->msg;
                $log->save();


                /*Order Detail*/
//                foreach ($product as $key => $value) {

                $details = new OrderDetail();
                $details->order_id = $orderId;
                $details->product_id = $product;
                $details->price = getProductPrice($product);
                $details->quantity = $quantity;
                $details->code = $code;
                $details->product_coupon_discount = $request->product_coupon_discount;
                $details->delivery_status = 1;
                $details->payment_status = 1;
                $details->attribute = ($attribute) ? $attribute : '';
                $details->save();
//                }


                DB::commit();
                return ApiResponse::sendSuccessResponse('Order Created Successfully');
            } catch (Exception $e) {
                DB::rollBack();
                return ApiResponse::sendExceptionResponse($e->getMessage());
            }
        }
    }

    public function orderTracking(Request $request)
    {
        $rules = array(
            'code' => 'required',
        );

        $messages = array(
            'code.required' => 'Order Code is required.',
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messages = $validator->messages();
            $errors = $messages->all();

            return ApiResponse::sendValidationErrorResponse($errors);
        } else {

            $checkCode = Order::where('user_id', auth()->user()->id)->where('code', $request->code)->first();

            if (!empty($checkCode->code)) {

                $info = OrderLog::select('log')->where('code', $request->code)
                    ->orderBy('created_at', 'ASC')->get();

                $logData = [];
                foreach ($info as $key => $value) {
                    $logData[] = $value->log;
                }

                return ApiResponse::sendSuccessResponse('Success', $logData);
            } else {
                return ApiResponse::sendNoDataErrorResponse('Order Code Not found.');
            }
        }
    }

    public function orderStatus()
    {
        $info = OrderStatus::select('id', 'name')->orderBy('id', 'ASC')->get();

        return ApiResponse::sendSuccessResponse('Success', $info);

    }

  public function  quickOrder(Request $request){

      try {

          $rules=array(
              'name'=>['required'],
              'email'=>['required','email'],
              'contact'=>['required','max:10','min:10'],
          );


          $validator=Validator::make($request->all(),$rules);

          if($validator->fails()){
              $messages=$validator->messages();
              $errors=$messages->all();

              return ApiResponse::sendValidationErrorResponse($errors);
          }

          DB::beginTransaction();

               $user = User::where('email',$request->email)->orWhere('contact',$request->contact)->first();

              if ($user){

                  if (!($user->is_verified)){
                      $user->update([
                          'is_verified'=>true
                      ]);
                  }
                  \Auth::login($user);

                  $checkUserId = DB::table('user_token')->where('user_id', \Auth::user()->id)->first();

                  if (!empty($checkUserId->user_id)) {

                      $response =  self::reuseQuickOrder($request,$checkUserId->token);

                      $data = array(
                          'authenticated' => true,
                          'user_info' => $response['info'],
                          'product'=>$response['product']
                      );
        DB::commit();
                      return ApiResponse::sendSuccessResponse('you have already been logged in', $data);

                  } else{

                      \Auth::login($user);

                      $token = \Auth::user()->createToken(\Auth::user()->id)->plainTextToken;


                      \Auth::user()->token = $token;

                      /*User Auth Token Registration*/
                      $this->saveUserToken($token);

                      $response = self::reuseQuickOrder($request,$token);

                      $data = array(
                          'authenticated' => true,
                          'user_info' => $response['info'],
                          'product'=>$response['product']
                      );
        DB::commit();
                      return ApiResponse::sendSuccessResponse('you are logged in', $data);
                  }
              }else {

                  $newUser = User::create([
                      'name' => $request->name,
                      'email' => $request->email,
                      'contact' => $request->contact,
                      'password' => bcrypt($request->contact),
                      'is_verified' => 1,
                      'is_admin' => 0,
                      'is_active' => 1,
//                      'profile_image'=>null
                  ]);

                  Auth::login($newUser);

                  $token = \Auth::user()->createToken(\Auth::user()->id)->plainTextToken;
                  Auth::user()->token = $token;
                  /*User Auth Token Registration*/
                  $this->saveUserToken($token);

                  $response = self::reuseQuickOrder($request, $token);

                  $data = array(
                      'authenticated' => true,
                      'user_info' => $response['info'],
                      'product' => $response['product']
                  );

//                  Mail::to($user->email)->send(new UserPasswordSend());
        DB::commit();
                  return ApiResponse::sendSuccessResponse('account created and  logged in', $data);
          }


      } catch (\Exception $exception) {

          DB::rollBack();
          $output['msg'] = $exception->getMessage();
      }

  }

    function saveUserToken($token)
    {

        $auth = new UserTokenModel();
        $auth->user_id = \Auth::user()->id;
        $auth->token = $token;
        $auth->save();
    }

    function reuseQuickOrder($request, $token)
    {

        $info = array(
            'id' => \Auth::user()->id,
            'name' => \Auth::user()->name,
            'contact' => \Auth::user()->contact,
            'email' => \Auth::user()->email,
            'profile_image' => (null == \Auth::user()->profile_image) ? '' : url('uploads/profile') . '/' . \Auth::user()->profile_image,
            'is_active' => \Auth::user()->is_active,
            'is_verified' => \Auth::user()->is_verified,
            'token' => $token,
        );


        $order = QuickOrder::create([
            'user_id' => \auth()->user()->id,
            'product_id' => $request->product_id,
            'price' => getProductPrice($request->product_id),
            'quantity' => $request->qty,
            'attribute' => $request->attribute_id
        ]);


        $attributeData = [
            'attribute_name' => $order->productAttribute->attribute->name,
            'product_attribute_name' => $order->productAttribute->value,
            'product_attribute_id' => $request->attribute_id,

        ];
        $product = [
            'id' => $order->id,
            'product_id' => $order->product_id,
            'product_name' => getProductName($order->product_id),
            'product_image' => asset('uploads/products/' . getProductImage($order->product_id)),
            'qty' => $request->qty,
            'price' => $order->price,
            'attribute' => $attributeData,
        ];

        $data = [
            'info' => $info,
            'product' => $product
        ];

        return $data;
    }
}