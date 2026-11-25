<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyUserOtp;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\ApiResponse\ApiResponse;

use App\Models\Notifications;
use App\Models\User;
use App\Models\UserVerificationSms;

use App\Models\UserTokenModel;

use Illuminate\Support\Facades\DB;

use Auth;
use Exception;

class LoginApiController extends Controller{

  public function checkUserAuthToken(Request $request){
    $rules=array(
      'auth_token' => 'required',
    );

    $messages=array(
      'auth_token.required' => 'Auth Token is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $checkToken = DB::table('user_token')
      ->where('token', $request->auth_token)->first();

      if (!empty($checkToken->token)) {
        return ApiResponse::sendSuccessResponse('Success', 'true');
      }
      else{
        return ApiResponse::sendSuccessResponse('Account has been logged-in from another device', 'false');
      }
    }
  }

  public function login(Request $request){
    $rules=array(
      'email' => 'required',
      'password' => 'required',
    );

    $messages=array(
      'email.required' => 'Email / Contact Number is required.',
      'password.required' => 'Password is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $credentials = array(
        "password" => $request->password,
        "is_active" => 1,
        'is_admin' => 0,
      );

      if (preg_match("/^-?[0-9]+$/", $request->email)) {
        $credentials['contact'] = $request->email;
      }
      else{
        $credentials['email'] = $request->email;
      }

      if(Auth::attempt($credentials)){

//        $checkUserId = DB::table('user_token')->where('user_id', Auth::user()->id)->first();
//
//        if (!empty($checkUserId->user_id)) {
//
//          $info = array(
//            'id' => Auth::user()->id,
//            'name' => Auth::user()->name,
//            'contact' => Auth::user()->contact,
//            'email' => Auth::user()->email,
//            'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.Auth::user()->profile_image,
//            'is_active' => Auth::user()->is_active,
//            'is_verified' => Auth::user()->is_verified,
//          );
//
//
//          $data = array(
//            'authenticated' => false,
//            'user_info' => $info,
//          );
//
//          return ApiResponse::sendSuccessResponse('Your account is already active in another device. Do you want to clear account from other devices ?', $data);
//        }
//        else{
          $token = Auth::user()->createToken(Auth::user()->id)->plainTextToken;
          Auth::user()->token = $token;

          /*User Auth Token Registration*/
//          $this->saveUserToken($token);


          $info = array(
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'contact' => Auth::user()->contact,
            'email' => Auth::user()->email,
            'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.Auth::user()->profile_image,
            'is_active' => Auth::user()->is_active,
            'is_verified' => Auth::user()->is_verified,
            'token' => $token,
          );

          $data = array(
            'authenticated' => true,
            'user_info' => $info,
          );

          return ApiResponse::sendSuccessResponse('Login Success', $data);
        }
//      }
      else{
        return ApiResponse::sendNoDataErrorResponse('Invalid Credentials or Inactive User');
      }
    }
  }

  public function sendAccountResetOTP(Request $request){
    $rules=array(
      'user_id' => 'required',
    );

    $messages=array(
      'user_id.required' => 'User is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try{

        $countVerification = UserVerificationSms::where('user_id', $request->user_id)->get();

        if (count($countVerification) >= 3) {
          return ApiResponse::sendExceptionResponse('Verification Limit Exceeded. Max Verification Limit is 3 times');
        }

        $userInfo = User::where('id', $request->user_id)
        ->where('is_active', 1)
        ->where('is_admin', 0)->where('is_verified', 1)->first();

        $code = substr(number_format(time() * rand(),0,'',''),0,6);

        $data = array(
          'to' => $userInfo->contact,
          'msg' => 'Saman Sansar - Account Reset OTP is: '.$code,
        );

        $result = sendSMS($data);

        if ($result['response']) {
          $verification = new UserVerificationSms;
          $verification->user_id = $request->user_id;
          $verification->code = $code;
          $verification->contact = $userInfo->contact;
          $verification->created_date = date('Y-m-d H:i:s');
          $verification->save();

          DB::commit();
          return ApiResponse::sendSuccessResponse('Verification Code sent Successfully', $request->user_id);
        }
        else{
          throw new Exception($result['msg']);
        }
      }catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function verifyAccountResetOTP(Request $request){
    $rules=array(
      'code' => 'required|max:6',
      'user_id' => 'required',
      'password' => 'required',
    );

    $messages=array(
      'code.required' => 'Code is required.',
      'user_id.required' => 'User ID is required.',
      'password.required' => 'Password is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try {

        $check = UserVerificationSms::select('code', 'contact')
        ->where('user_id', $request->user_id)->orderBy('created_date', 'DESC')->first();

        if (!empty($check)) {

          if ($check->code == $request->code) {

            /*delete From SMS Log*/
            DB::table('user_token')->where('user_id', $request->user_id)->delete();


            $info = User::where('id', $request->user_id)->first();
            $credentials = [
              "email" => $info->email,
              "password" => $request->password,
              "is_active" => 1,
            ];

            if(Auth::attempt($credentials)){
              $token = Auth::user()->createToken($request->user_id)->plainTextToken;
              Auth::user()->token = $token;


              /*User Auth Token Registration*/
              $auth = new UserTokenModel();
              $auth->user_id = $request->user_id;
              $auth->token = $token;
              $auth->save();


              /* Delete From SMS Log */
              DB::table('user_verification_sms')->where('user_id', $request->user_id)->delete();


              $info = array(
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'contact' => Auth::user()->contact,
                'email' => Auth::user()->email,
                'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.Auth::user()->profile_image,
                'is_active' => Auth::user()->is_active,
                'is_verified' => Auth::user()->is_verified,
                'token' => $token,
              );


              $data = array(
                'authenticated' => true,
                'user_info' => $info,
              );

              DB::commit();
              return ApiResponse::sendSuccessResponse('User Verified Successfully', $data);
            }
          }
          else{
            throw new Exception("Invalid Verification Code");
          }
        }
        else{
          throw new Exception("Invalid Verification Code");
        }
      } catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function signup(Request $request){

//      return $request->all();

    $rules=array(
      'name' => 'required|string',
      'email' => 'required|string|email|unique:users,email',
      'contact' => 'required|unique:users,contact|max:10|min:10',
      'password' => 'required|min:8',
    );

    $messages=array(
      'name.required' => 'Full Name is required.',
      'email.required' => 'Email is required.',
      'contact.required' => 'Contact Number is required.',
      'password.required' => 'Password is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try{


        /*User registration*/
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact = $request->contact;
        $user->password = bcrypt($request->password);
        $user->is_active = 1;
        $user->is_verified = 0;
        $user->is_admin = 0;
        $user->type = 'normal';
        $user->save();


        /*New Code - Direct Login after Signup without otp verification*/
        $credentials = [
          "email" => $request->email,
          "password" => $request->password,
          "is_active" => 1,
          'is_admin' => 0,
//          'type' => 'normal',
        ];
//        return  $credentials;
        if(Auth::attempt($credentials)){
         $token = Auth::user()->createToken($user->id)->plainTextToken;
         Auth::user()->token = $token;


          /*User Auth Token Registration*/
         $this->saveUserToken($token);


          $info = array(
            'id' => $user->id,
            'name' => $user->name,
            'contact' => $user->contact,
            'email' => $user->email,
            'profile_image' => (null==$user->profile_image) ? '' : url('uploads/profile').'/'.$user->profile_image,
            'is_active' => $user->is_active,
            'is_verified' => $user->is_verified,
           'token' => $token,
          );


           $code = substr(number_format(time() * rand(),0,'',''),0,6);
//            return $code;
//            $result = sendSMS($data);

           $response =  \Mail::to($user->email)
               ->send(new VerifyUserOtp($user, $code));

        //    if ($result['response']) {
               $verification = new UserVerificationSms;
               $verification->user_id = $user->id;
               $verification->code = $code;
               $verification->contact = $request->contact;
               $verification->email = $request->email;
               $verification->created_date = date('Y-m-d H:i:s');
               $verification->save();
        //    }
          DB::commit();

          $data = array(
                'otp_code' => $code,
                'user_info' => $info,
            );

          return ApiResponse::sendSuccessResponse('Signup Complete Successfully', $data);
        }


        /*Old Code - To send otp code for signup verification*/

        /*$countVerification = UserVerificationSms::where('user_id', $user->id)->get();

        if (count($countVerification) >= 3) {
          return ApiResponse::sendExceptionResponse('Verification Limit Exceeded. Max Verification Limit is 3 times');
        }

        $code = substr(number_format(time() * rand(),0,'',''),0,6);

        $data = array(
          'to' => $request->contact,
          'msg' => 'Saman Sansar - Signup verification OTP is: '.$code,
        );

        $result = sendSMS($data);

        if ($result['response']) {
          $verification = new UserVerificationSms;
          $verification->user_id = $user->id;
          $verification->code = $code;
          $verification->contact = $request->contact;
          $verification->created_date = date('Y-m-d H:i:s');
          $verification->save();

          DB::commit();
          return ApiResponse::sendSuccessResponse('Verification Code sent Successfully', $user->id);
        }
        else{
        //   throw new Exception($result['msg']);
        // }*/

      }catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function verifyAccountSignup(Request $request){
    $rules=array(
      'code' => 'required|max:6',
      'user_id' => 'required',
      /*'password' => 'required',*/
    );

    $messages=array(
      'code.required' => 'Code is required.',
      'user_id.required' => 'User ID is required.',
      /*'password.required' => 'Password is required.',*/
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try {

        $check = UserVerificationSms::select('code', 'contact','email')
        ->where('user_id', $request->user_id)->orderBy('created_date', 'DESC')->first();

        if (!empty($check)) {

          if ($check->code == $request->code) {




            // /*delete From SMS Log*/
            // DB::table('user_verification_sms')->where('user_id', $request->user_id)->delete();

            // DB::commit();
            // return ApiResponse::sendSuccessResponse('User Verified Successfully');

            /*Old - User Signup Verififcation after entry of OTP code*/
            $info = User::where('id', $request->user_id)->first();

            // $credentials = [
            //   "email" => $info->email,
            //   "password" => $info->password,
            //   "is_active" => 1,
            //   'is_admin' => 0,
            // ];



            if($info){
                Auth::login($info);
              $token = Auth::user()->createToken($request->user_id)->plainTextToken;
              Auth::user()->token = $token;

                /*Update User Tbl*/ 
            $user = User::find($request->user_id);
            $user->is_verified = 1;
            $user->save();

              $auth = new UserTokenModel();
              $auth->user_id = $request->user_id;
              $auth->token = $token;
              $auth->save();


              $info = array(
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'contact' => Auth::user()->contact,
                'email' => Auth::user()->email,
                'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.Auth::user()->profile_image,
                'is_active' => Auth::user()->is_active,
                'is_verified' => Auth::user()->is_verified,
                'token' => $token,
              );


              DB::commit();
              return ApiResponse::sendSuccessResponse('User Verified Successfully',$info);
            }else{
                return  ApiResponse::sendNoDataErrorResponse("user doesnot exists");
            }
          }
          else{

            return ApiResponse::sendExceptionResponse("Invalid Verification Code");
          }
        }
        else{

            return ApiResponse::sendExceptionResponse("Invalid Verification Code");
        }
      } catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function resendSignupOTP(Request $request){
    $rules=array(
      'user_id' => 'required',
    );

    $messages=array(
      'user_id.required' => 'User is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try{

        $countVerification = UserVerificationSms::where('user_id', $request->user_id)->get();

        if (count($countVerification) >= 3) {
          return ApiResponse::sendExceptionResponse('Verification Limit Exceeded. Max Verification Limit is 3 times');
        }

        $userInfo = User::where('id', $request->user_id)
        ->where('is_active', 1)
        ->where('is_admin', 0)->where('is_verified', 0)->first();

        $code = substr(number_format(time() * rand(),0,'',''),0,6);

        // $data = array(
        //   'to' => $userInfo->contact,
        //   'msg' => 'Saman Sansar - Signup verification OTP is: '.$code,
        // );

        // $result = sendSMS($data);

        // if ($result['response']) {
          $verification = new UserVerificationSms;
          $verification->user_id = $request->user_id;
          $verification->code = $code;
          $verification->contact = $userInfo->contact ?? '';
          $verification->created_date = date('Y-m-d H:i:s');
          $verification->save();

          DB::commit();

          $data = [
            'user_id' => $request->user_id,
            'otp_code' =>$code
          ];

          return ApiResponse::sendSuccessResponse('Verification Code sent Successfully', $data);

        // }
        // else{
        //   throw new Exception($result['msg']);
        // }
      }catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function logout(Request $request){

    DB::beginTransaction();

    try{

      DB::table('user_token')->where('user_id', auth()->user()->id)->delete();

      auth()->user()->tokens()->delete();

      DB::commit();
      return ApiResponse::sendSuccessResponse('Logout Success');

    } catch (Exception $e) {
      DB::rollBack();
      return ApiResponse::sendExceptionResponse($e->getMessage());
    }
  }

  public function userProfile(){
    $info = User::where('id', auth()->user()->id)->first();

    $data = array(
      'id' => $info->id,
      'name' => $info->name,
      'contact' => $info->contact,
      'email' => $info->email,
      'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.$info->profile_image,
      'is_active' => $info->is_active,
      'is_verified' => $info->is_verified,
    );

    return ApiResponse::sendSuccessResponse('Success', $data);
  }

  public function editProfile(Request $request){
    if($request->full_name){
      $rules=array(
        'full_name' => 'required',
      );

      $messages=array(
        'full_name.required' => 'Full Name is required.',
      );
    }

    if($request->email){
      $rules=array(
        'email' => 'required|email|max:255|unique:users,email,' . auth()->user()->id,
      );

      $messages=array(
        'email.required' => 'Email is required.',
      );
    }

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $user = User::find(auth()->user()->id);

      if ($request->full_name) {
        $user->name = $request->full_name;
      }

      if ($request->email) {
        $user->email = $request->email;
      }

      $user->save();

      return ApiResponse::sendSuccessResponse('Profile Updated Successfully');
    }
  }

  public function userNotificationList(Request $request){
    $info = Notifications::select('title', 'message', 'created_at')
    ->skip($request->offset * $request->limit)->take($request->limit)
    ->orderBy('created_at', 'DESC')->get();

    if (! $info->isEmpty()) {
      return ApiResponse::sendSuccessResponse('Success', $info);
    }
    else{
      return ApiResponse::sendNoDataErrorResponse('No Data found.');
    }
  }

  public function editUserContact(Request $request){
    $rules=array(
      'contact' => 'required|max:10|unique:users,contact,' . auth()->user()->id,
    );

    $messages=array(
      'contact.required' => 'Contact is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try {

        $countVerification = UserVerificationSms::where('user_id', auth()->user()->id)->get();

        if (count($countVerification) >= 3) {
          return ApiResponse::sendExceptionResponse('Verification Limit Exceeded. Max Verification Limit is 3 times');
        }

        $code = substr(number_format(time() * rand(),0,'',''),0,6);

        $data = array(
          'to' => $request->contact,
          'msg' => 'Saman Sansar verification OTP is: '.$code,
        );

        $result = sendSMS($data);

        if ($result['response']) {

          $verification = new UserVerificationSms;
          $verification->user_id = auth()->user()->id;
          $verification->code = $code;
          $verification->contact = $request->contact;
          $verification->created_date = date('Y-m-d H:i:s');
          $verification->save();

          DB::commit();
          return ApiResponse::sendSuccessResponse('Verification Code sent Successfully');
        }
        else{
          throw new Exception($result['msg']);
        }
      } catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function verifyEditContact(Request $request){
    $rules=array(
      'code' => 'required|max:6',
    );

    $messages=array(
      'code.required' => 'Code is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try {

        $check = UserVerificationSms::select('code', 'contact')->where('user_id', auth()->user()->id)->orderBy('created_date', 'DESC')->first();

        if ($check->code == $request->code) {

          $user = User::find(auth()->user()->id);
          $user->contact = $check->contact;
          $user->is_verified = 1;
          $user->save();

          DB::table('user_verification_sms')->where('user_id', auth()->user()->id)->delete();

          DB::commit();
          return ApiResponse::sendSuccessResponse('Contact Number Updated Successfully');
        }
        else{
          throw new Exception("Invalid Verification Code");
        }
      } catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function changeUserPassword(Request $request){
    $rules=array(
      'curr_password' => 'required|string',
      'new_password' => 'required|string|min:8',
    );

    $messages=array(
      'curr_password.required' => 'Old Password is required.',
      'new_password.required' => 'New Password is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $getCurrPass = User::select('password')->where('id', auth()->user()->id)->first();

      if (password_verify($request->curr_password, $getCurrPass->password)) {

        if ($request->curr_password != $request->new_password) {

          $user = User::find(auth()->user()->id);
          $user->password = bcrypt($request->new_password);
          $user->save();

          return ApiResponse::sendSuccessResponse('Password Changed Successfully');
        }
        else{
          return ApiResponse::sendExceptionResponse('OLD PASSWORD !!! Please Enter A Unique Password');
        }
      }
      else{
        return ApiResponse::sendExceptionResponse('Incorrect Current Password. Please try again');
      }
    }
  }

  public function forgotUserPassword(Request $request){
    $rules=array(
      'contact' => 'required',
    );


    $messages=array(
      'contact.required' => 'Contact Number is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      DB::beginTransaction();

      try{

        $check = User::where('contact', $request->contact)
        ->where('is_active', 1)
        // ->where('is_verified', 1)
        ->where('is_admin', 0)->first();

        if (!empty($check->contact)) {

          $countVerification = UserVerificationSms::where('user_id', $request->user_id)->get();

          if (count($countVerification) >= 3) {
            return ApiResponse::sendExceptionResponse('Verification Limit Exceeded. Max Verification Limit is 3 times');
          }

          $code = substr(number_format(time() * rand(),0,'',''),0,6);

          $data = array(
            'to' => $check->contact,
            'msg' => 'Saman Sansar - Forgot Password OTP is: '.$code,
          );

        //   $result = sendSMS($data);

        //   if ($result['response']) {
            $verification = new UserVerificationSms;
            $verification->user_id = $check->id;
            $verification->code = $code;
            $verification->contact = $check->contact;
            $verification->created_date = date('Y-m-d H:i:s');
            $verification->save();

            $data = [
                'user_id'=>$request->user_id,
                'code'=>$code
            ];

            DB::commit();
            return ApiResponse::sendSuccessResponse('Verification Code sent Successfully', $data);
        //   }
        //   else{
        //     throw new Exception($result['msg']);
        //   }
        }
        else{
          throw new Exception("Invalid Contact Number");
        }
      }catch (Exception $e) {
        DB::rollBack();
        return ApiResponse::sendExceptionResponse($e->getMessage());
      }
    }
  }

  public function verifyForgotPasswordOtp(Request $request){
    $rules=array(
      'contact' => 'required|numeric',
      'code' => 'required|max:6',
    );

    $messages=array(
      'contact.required' => 'Contact Number is required.',
      'code.required' => 'OTP Code is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{

      $check = UserVerificationSms::select('code', 'contact', 'user_id')
      ->where('contact', $request->contact)->orderBy('created_date', 'DESC')->first();

      if (!empty($check)) {

        if ($check->code == $request->code) {

          /*delete From SMS Log*/
          /*DB::table('user_verification_sms')->where('user_id', $check->user_id)->delete();*/

          return ApiResponse::sendSuccessResponse('User Verified Successfully', $check->user_id);
        }
        else{
          return ApiResponse::sendValidationErrorResponse("Invalid Verification Code");
        }
      }
      else{
        return ApiResponse::sendValidationErrorResponse("Invalid Verification Code");
      }
    }
  }

  public function addNewUserPassword(Request $request){
    $rules=array(
      'user_id' => 'required|numeric',
      'new_password' => 'required|min:8',
    );

    $messages=array(
      'user_id.required' => 'User Id is required.',
      'new_password.required' => 'New Password is required.',
    );

    $validator=Validator::make($request->all(),$rules,$messages);

    if($validator->fails()){
      $messages=$validator->messages();
      $errors=$messages->all();

      return ApiResponse::sendValidationErrorResponse($errors);
    }
    else{
      $user = User::find($request->user_id);
      $user->password = bcrypt($request->new_password);
      $user->save();

      return ApiResponse::sendSuccessResponse('Password Changed Successfully');
    }
  }

  function saveUserToken($token){
    $auth = new UserTokenModel();
    $auth->user_id = Auth::user()->id;
    $auth->token = $token;
    $auth->save();
  }
}