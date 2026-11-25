<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Validator;

use Image;

use Illuminate\Http\Request;

use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketIssueCategory;

use Carbon\Carbon;

class GeneralSettingApiController extends Controller{

	public function getLogo(){
		$logo = getSiteLogo();

		if($logo){
			$path = url('uploads/logo/');
			$logo = $path.'/'.$logo;
			return ApiResponse::sendSuccessResponse('Success',$logo,$path);
		}else{
			return ApiResponse::sendNoDataErrorResponse('Logo not found.');
		}
	}

	function updateProfilePicture(Request $request){

        return $request->file('profile_image');

		$rules=array(
			'profile_image' => 'required|mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
		);

		$messages=array(
			'profile_image.required' => 'Profile Image is required.',
		);

		$validator=Validator::make($request->all(),$rules,$messages);

		if($validator->fails()){
			$messages=$validator->messages();
			$errors=$messages->all();
			return ApiResponse::sendValidationErrorResponse($errors);
		}
		else{
			if ($request->file('profile_image')) {
				$extension = $request->profile_image->extension();
				$imageName = strtotime(date('Y-m-d H:i:s')).'.'.$extension;

				$img = Image::make($request->profile_image->path());

				$img->save(public_path('/uploads/profile') . '/' . $imageName);

				$user = User::find(auth()->user()->id);
				$user->profile_image = $imageName;
				$user->save();
				$path = url('uploads/profile/');

				$logo = $path.'/'.$imageName;

				return ApiResponse::sendSuccessResponse('Profile Image Uploaded Successfully', $logo, $path);
			}
			else{
				return ApiResponse::sendNoDataErrorResponse('profile Image Not Found !!!');
			}
		}
	}

	function createSupportTicket(Request $request){
		$rules=array(
			'issue_category_id' => 'required|numeric',
			'description' => 'required',
		);

		if ($request->file('image')) {
			$rules=array(
				'support_image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
			);
		}

		$messages=array(
			'issue_category_id.required' => 'Issue Category is required.',
			'description.required' => 'Description is required.',
		);

		$validator=Validator::make($request->all(),$rules,$messages);

		if($validator->fails()){
			$messages=$validator->messages();
			$errors=$messages->all();
			return ApiResponse::sendValidationErrorResponse($errors);
		}
		else{
			$ticket = new Ticket();

			$getRecentTicket = Ticket::orderBy('ticket_id','desc')->first();

			if($getRecentTicket){
				$ticket->ticket_no = $getRecentTicket->ticket_no + 1;
			}else{
				$ticket->ticket_no = 1000;
			}

			if ($request->file('support_image')) {
				$extension = $request->support_image->extension();
				$imageName = strtotime(date('Y-m-d H:i:s')) .'.'. $extension;

				$request->file('support_image')->move(public_path('/uploads/tickets'), $imageName);
				$ticket->image = $imageName;
			}

			$ticket->description = $request->description;
			$ticket->issue_category = $request->issue_category_id;
			$ticket->created_date = now();
			$ticket->created_by = auth()->user()->id;
			$ticket->save();

			return ApiResponse::sendSuccessResponse('Ticket Created Successfully');
		}
	}

	function ticketDetails(Request $request){
		$rules=array(
			'ticket_id' => 'required|numeric',
		);

		$messages=array(
			'ticket_id.required' => 'Ticket Id is required.',
		);

		$validator=Validator::make($request->all(),$rules,$messages);

		if($validator->fails()){
			$messages=$validator->messages();
			$errors=$messages->all();
			return ApiResponse::sendValidationErrorResponse($errors);
		}
		else{
			$tickets = Ticket::where('ticket_id', $request->ticket_id)
			->orWhere('parent_ticket_id', $request->ticket_id)->orderBy('ticket_id','asc')->get();

			$ticketArray = [];
			foreach ($tickets as $key => $value) {
				$ticketArray[] = array(
					'ticket_id' => $value->ticket_id,
					'ticket_no' => $value->ticket_no,
					'description' => $value->description,
					'created_date' => $value->created_date,
					'created_by' => $value->created_by,
					'image' => (null==$value->image) ? '' : url('uploads/tickets/').'/'.$value->image,
				);
			}

			return ApiResponse::sendSuccessResponse('Success', $ticketArray);
		}
	}

	function replyTicket(Request $request){
		$rules=array(
			'ticket_id' => 'required|numeric',
		);

		if ($request->file('image')) {
			$rules=array(
				'ticket_image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
			);
		}

		$messages=array(
			'ticket_id.required' => 'Ticket Id is required.',
		);

		$validator=Validator::make($request->all(),$rules,$messages);

		if($validator->fails()){
			$messages=$validator->messages();
			$errors=$messages->all();
			return ApiResponse::sendValidationErrorResponse($errors);
		}
		else{

			$ticket = new Ticket();

			$getRecentTicket = Ticket::orderBy('ticket_id','desc')->first();

			if($getRecentTicket){
				$ticket->ticket_no = $getRecentTicket->ticket_no + 1;
			}else{
				$ticket->ticket_no = 1000;
			}

			if ($request->file('ticket_image')) {
				$extension = $request->ticket_image->extension();
				$imageName = strtotime(date('Y-m-d H:i:s')).'.'.$extension;

				$request->file('ticket_image')->move(public_path('/uploads/tickets'), $imageName);
				$ticket->image = $imageName;
			}

			$ticket->parent_ticket_id = $request->ticket_id;
			$ticket->description = $request->message;
			$ticket->created_date = now();
			$ticket->created_by = auth()->user()->id;
			$ticket->save();

			return ApiResponse::sendSuccessResponse('Reply Message Sent Successfully');
		}
	}

	function ticketCategoryList(){
		$data = TicketIssueCategory::select('id', 'name')->where('is_active', 1)->get();

		return ApiResponse::sendSuccessResponse('Success', $data);
	}

	function myTicketList(){
		$data = Ticket::select('ticket.ticket_id', 'ticket.description', 'ticket.created_date', 'ticket.ticket_no', 'ticket.image', 'ticket_issue_category.name AS category_name')
		->leftJoin('ticket_issue_category', 'ticket_issue_category.id', '=', 'ticket.issue_category')
		->where('ticket.is_active', 1)
		->where('ticket.created_by', auth()->user()->id)
		->where('ticket.parent_ticket_id', 0)
		->orderBy('created_date', 'DESC')->get();

		$result = array();
		foreach ($data as $key => $value) {
			$result[] = array(
				'ticket_id' => $value->ticket_id,
				'description' => $value->description,
				'created_date' => $value->created_date,
				'ticket_no' => $value->ticket_no,
				'category_name' => $value->category_name,
				'image' => (null==$value->image) ? '' : url('uploads/tickets/').'/'.$value->image,
			);
		}

		if (!empty($result)) {
			return ApiResponse::sendSuccessResponse('Success', $result);
		}
		else{
			return ApiResponse::sendNoDataErrorResponse('No Data found.');
		}
	}
}
