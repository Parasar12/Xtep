<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

use App\Models\GuestUserModel;

class GuestCheckoutApiController extends Controller{
	
	public function registerUser(Request $request){
		$rules=array(
			'full_name' => 'required',
			'email' => 'required|email',
			'contact' => 'required|numeric',
			'note' => 'required',
		);

		$messages=array(
			'full_name.required' => 'Full Name is required.',
			'email.required' => 'Email is required.',
			'contact.required' => 'Contact is required.',
			'note.required' => 'Note is required.',
		);

		$validator=Validator::make($request->all(),$rules,$messages);

		if($validator->fails()){
			$messages=$validator->messages();
			$errors=$messages->all();
			return ApiResponse::sendValidationErrorResponse($errors);
		}
		else{
			$guest = new GuestUserModel();
			$guest->full_name = $request->full_name;
			$guest->email = $request->email;
			$guest->contact = $request->contact;
			$guest->note = $request->note; 
			$guest->_token = $request->note; 
			$guest->save();

			return ApiResponse::sendSuccessResponse('Guest User Created Successfully');
		}
	}
}
