<?php

namespace App\Http\Controllers;

use App\Mail\SendUserEmailCredentials;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CommonController extends Controller
{

    public function searchCustomer(Request $request): JsonResponse
    {

        $customers = \DB::table('users')
            ->select('id', 'name', 'contact')
            ->where([
                ['name', 'like', '%' . $request->q . '%'],
            ])
            ->orWhere('contact', 'like', '%' . $request->q . '%')
            ->limit(20)
            ->get();

        $result = array();
        if ($customers->count()) {
            foreach ($customers as $customer) {
                $row ['id'] = $customer->id;
                $row['text'] = $customer->name . '(' . $customer->contact . ')';

                $result[] = $row;
            }
        }


        $output = array(
            'result' => array('results' => $result),
            'response' => true,
            'msg' => '',
        );

        return \Response::json($output);
    }

    public function productSearch(Request $request): JsonResponse
    {

        $products = \DB::table('products')->where([
            ['name','like', '%' . $request->q .'%'],
            ['is_active',true]
        ])->get();

        $result = array();
        if ($products->count()) {
            foreach ($products as $product) {
                $row ['id'] = $product->id;
                $row['text'] = $product->name;

                $result[] = $row;
            }
        }

        $output = array(
            'result' => array('results' => $result),
            'response' => true,
            'msg' => '',
        );

        return \Response::json($output);
    }

    public function getProductPrice(Request $request): JsonResponse
    {

        $output = array(
            'result' => '',
            'response' => false,
            'msg' => '',
        );

        try {
            $product = \DB::table('product_masters')
                ->where([
                    ['product_id', $request->product_id],
                    ['is_active',true]
                ])->first();

            $output['response'] = true;
            $output['result'] = $product->price;
        } catch (\Exception $e) {
            $output['msg']  = $e->getMessage();
        }

        return  Response::json($output);
    }

    public function getCouponDiscountPrice(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

           $totalCouponDiscount = 0;
            if(in_array($request->product_id,getCouponProducts($request->coupon_id))){

                $couponPrice = getCouponDiscountedPrice($request->coupon_id,$request->product_id,$request->qty);
                $couponDiscountedPrice = $request->qty * $couponPrice;
                $productCouponDiscount = (getProductPrice($request->product_id) * $request->qty) - ($couponDiscountedPrice);
                $totalCouponDiscount = $productCouponDiscount;
            }

            if(in_array($request->product_id,getCouponCategories($request->coupon_id))){
                $couponPrice = getCouponDiscountedPriceByCategory($request->coupon_id,$request->product_id,$request->qty);
                $couponDiscountedPrice = $couponPrice;
                $productCouponDiscount = (getProductPrice($request->product_id) * $request->qty) - ($couponDiscountedPrice);
                $totalCouponDiscount = $productCouponDiscount;
            }
            $data = [
                'total_price'=>$couponDiscountedPrice,
                'total_coupon_discount'=>$totalCouponDiscount
            ];

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $data;

        } catch (\Exception $exception) {

            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function save_user_from_order(Request $request){

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'name'=>['required','max:30'],
                'contact_number'=>['required'],
                'email'=>['required','unique:users,email']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }
            DB::beginTransaction();

            $user =  User::create([
                'name'=>$request->name,
                'contact'=>$request->post('contact_number'),
                'secondary_contact'=>$request->post('secondary_contact'),
                'password'=>Hash::make($request->contact_number),
                'email'=>$request->post('email'),
                'is_active'=>true,
                'is_verified'=>true
            ]);

//            Mail::to($user->email)->send(new SendUserEmailCredentials($request->contact_number,$user));

            DB::commit();
            $output['response'] = true;
            $output['msg'] = 'user added  successfully';
            $output['result'] = $user;

        } catch (\Exception $exception) {

            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function vendors_search(Request $request): JsonResponse
    {

        $vendors = \DB::table('vendors')->where([
            ['name','like', '%' . $request->keyword .'%'],
            ['status',true]
        ])->get();

        $result = array();
        if ($vendors->count()) {
            foreach ($vendors as $vendor) {
                $row ['id'] = $vendor->id;
                $row['text'] = $vendor->name;

                $result[] = $row;
            }
        }

        $output = array(
            'result' => array('results' => $result),
            'response' => true,
            'msg' => '',
        );

        return \Response::json($output);
    }
}
