<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    public function sendOtp(Request $request): JsonResponse
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'phone' => 'required|digits:10'
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            if(env('NO_OTP')){

                Session::put('OTP', '123456');
                Session::put('PHONE', $request->get('phone'));
                $output['response'] = true;
                $output['msg'] = 'your otp is 123456';
            }else{
                $otp = rand(111111, 999999);
                $sms = [
                    'to' => $request->get('phone'),
                    'msg' => 'Your OTP Verification Code is ' . $otp . ' . ' . getSiteTitle(),
                ];
                $sessionData = [
                    'phone' => $request->get('phone'),
                    'otp' => $otp
                ];

                $response = sendSMS($sms);

                Session::put('OTP', $otp);
                Session::put('PHONE', $request->get('phone'));

                if ($response['response'] == true){
                    $output['response'] = true;
                    $output['msg'] = '';
                }else{
                    $output['response'] = true;
                    $output['msg'] = 'Invalid phone Number';
                }
            }


        } catch (\Exception $exception) {

            $output['msg'] = $exception->getMessage();
        }

        return  Response::json($output);
    }

    public function verify_otp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);
        $otp = $request->session()->get('OTP');
        if ($request->otp == $otp) {
            Session::forget('OTP');
            return response()->json(['status' => true, 'message' => '']);
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid OTP. Please Enter Valid OTP.']);
        }
    }
}
