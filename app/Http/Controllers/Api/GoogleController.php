<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

use Image;
use Auth;
use Exception;
use Google_Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\UserTokenModel;

use Carbon\Carbon;

class GoogleController extends Controller{

	function verifyGmailUser(Request $request){
		$rules=array(
			'google_token' => 'required',
		);

		$messages=array(
			'google_token.required' => 'Google Token is required.',
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

				/*Specify the CLIENT_ID of the app that accesses the backend*/
				$client = new Google_Client(['client_id' => "252525778457-qsq8qqlb45pfbgbaj98mfpqnrulf7nve.apps.googleusercontent.com"]);

				$payload = $client->verifyIdToken($request->google_token);


				if ($payload) {

					$checkEmail = User::where('email', $payload['email'])->where('google_id', $payload['sub'])->where('is_active', 1)->where('is_admin', 0)->first();

					if (!empty($checkEmail)) {

						$data = Auth::login($checkEmail);

						$token = Auth::user()->createToken(Auth::user()->id)->plainTextToken;
						Auth::user()->token = $token;


						if(UserTokenModel::where('user_id', '=', Auth::user()->id)->exists()){
							UserTokenModel::where('user_id', auth()->user()->id)
							->update([
								'token' => $token,
							]);
						}
						else{
							$this->saveUserToken($token);
						}

						$data = array(
							'authenticated' => true,
							'user_info' => $this->userSessionData($token),
						);
					}
					else{

						$checkGoogleId = User::where('email', $payload['email'])->whereNull('google_id')
						->where('is_active', 1)->where('is_admin', 0)->first();

						if (!empty($checkGoogleId)) {

							$data = Auth::login($checkGoogleId);

							$token = Auth::user()->createToken(Auth::user()->id)->plainTextToken;
							Auth::user()->token = $token;


							if(UserTokenModel::where('user_id', '=', Auth::user()->id)->exists()){
								UserTokenModel::where('user_id', auth()->user()->id)
								->update([
									'token' => $token,
								]);
							}
							else{
								$this->saveUserToken($token);
							}

							$data = array(
								'authenticated' => true,
								'user_info' => $this->userSessionData($token),
							);

						}
						else{

							$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
							$pass = substr(str_shuffle($str_result), 0, 10);


							$filename = basename($payload['picture']).'.png';
							Image::make($payload['picture'])->save(public_path('uploads/profile/' . $filename));


							$user = new User;
							$user->name = $payload['name'];
							$user->email = $payload['email'];
							$user->password = bcrypt($pass);
							$user->profile_image = $filename;
							$user->google_id = $payload['sub'];
							$user->is_active = 1;
							$user->save();


							$credentials = array(
								"email" => $user->email,
								"password" => $pass,
								"is_active" => 1,
								'is_admin' => 0,
							);

							if(Auth::attempt($credentials)){

								$token = Auth::user()->createToken(Auth::user()->id)->plainTextToken;
								Auth::user()->token = $token;

								/*User Auth Token Registration*/
								$this->saveUserToken($token);

								$data = array(
									'authenticated' => true,
									'user_info' => $this->userSessionData($token),
								);
							}
						}
					}

					DB::commit();
					return ApiResponse::sendSuccessResponse('Login Success', $data);

				} else {
					throw new Exception("Invalid Google Authentication");
				}

			} catch (Exception $e) {
				DB::rollBack();
				return ApiResponse::sendExceptionResponse($e->getMessage());
			}
		}
	}

	function saveUserToken($token){
		$auth = new UserTokenModel();
		$auth->user_id = Auth::user()->id;
		$auth->token = $token;
		$auth->save();
	}

	function userSessionData($token){
		$data = array(
			'id' => Auth::user()->id,
			'name' => Auth::user()->name,
			'contact' => Auth::user()->contact,
			'email' => Auth::user()->email,
			'profile_image' => (null==Auth::user()->profile_image) ? '' : url('uploads/profile').'/'.Auth::user()->profile_image,
			'is_active' => Auth::user()->is_active,
			'is_verified' => Auth::user()->is_verified,
			'token' => $token,
		);

		return $data;
	}

}
