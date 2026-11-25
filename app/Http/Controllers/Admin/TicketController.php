<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\SocialSite;
use App\Models\TicketIssueCategory;
use App\Models\Ticket;

use Illuminate\Support\Facades\Validator;

class TicketController extends Controller{
	public function index(){
		return view('admin.ticket.index');
	}

	public function getTicket(Request $request){
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
		$totalRecords = Ticket::join('ticket_issue_category', 'ticket.issue_category', '=', 'ticket_issue_category.id')
		->select('count(*) as allcount')->count();
		$totalRecordswithFilter = Ticket::join('ticket_issue_category', 'ticket.issue_category', '=', 'ticket_issue_category.id')->select('count(*) as allcount')->where('ticket_issue_category.name', 'like', '%' . $searchValue . '%')->count();

		/*Fetch records*/
		if ($request->length == -1) {
			$records = Ticket::join('users', 'orders.user_id', '=', 'users.id')->orderBy($columnName, $columnSortOrder)
			->where('ticket_issue_category.name', 'like', '%' . $searchValue . '%')
			->select('ticket.ticket_id', 'ticket.ticket_no', 'ticket.created_date', 'ticket.is_active', 'ticket_issue_category.name')
			->take($rowperpage)
			->get();
		} else {
			$records = Ticket::join('ticket_issue_category', 'ticket.issue_category', '=', 'ticket_issue_category.id')->orderBy($columnName, $columnSortOrder)
			->where('ticket_issue_category.name', 'like', '%' . $searchValue . '%')
			->select('ticket.ticket_id', 'ticket.ticket_no', 'ticket.created_date', 'ticket.is_active', 'ticket_issue_category.name')
			->skip($start)
			->take($rowperpage)
			->get();
		}

		$data_arr = array();

		$i = 1;
		foreach ($records as $record) {
			$id = $record->ticket_id;
			$tNo = $record->ticket_no;
			$ticName = $record->name;
			$tDate = $record->created_date;
			$is_active = $record->is_active;


			if ($is_active == 1) {
				$status = '<span class="badge badge-pill badge-soft-success font-size-11">Open</span>';
			} else {
				$status = '<span class="badge badge-pill badge-soft-danger font-size-11">Closed</span>';
			}

			$data_arr[] = array(
				"id" => $i,
				"ticket_no" => $tNo,
				"ticName" => $ticName,
				"tDate" => $tDate,
				"is_active" => $status,
				'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('ticket.reply', $id) . "'>Reply</a>"
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
		$validator = Validator::make($request->all(), [
			'issue_category_id' => 'required|numeric',
			'description' => 'required|max:2000',
			'image' => 'mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
		}else{
			$ticket = new Ticket();

			$getRecentTicket = Ticket::orderBy('ticket_id','desc')->first();
			if($getRecentTicket){
				$ticket->ticket_no = $getRecentTicket->ticket_no + 1;
			}else{
				$ticket->ticket_no = 1000;
			}

			if ($request->file('image')) {

				$extension = $request->image->extension();
				$imageName = date('m-d-Y_H-i-s') . $request->file('image')->getClientOriginalName();

				$request->file('image')->move(public_path('/uploads/tickets'), $imageName);

				$ticket->image = $imageName;
			}

			$ticket->description = $request->description;
			$ticket->issue_category = $request->issue_category_id;
			$ticket->created_date = now();
			$ticket->created_by = auth()->user()->id;
			$ticket->save();

			return response()->json(['status' => 1, 'msg' => 'Ticket Created Successfully.']);
		}
	}

	public function ticketConversations($id){
		$tickets = Ticket::where('ticket_id',$id)->orWhere('parent_ticket_id',$id)->orderBy('ticket_id','asc')->get();
		return view('admin.ticket.conversations',compact('tickets','id'));
	}

	public function ticketDetails($id){
		$tickets = Ticket::where('ticket_id',$id)->orWhere('parent_ticket_id',$id)->orderBy('ticket_id','asc')->get();
		return view('frontend.ticket_details',compact('tickets','id'));
	}

	public function ticketReplyStore(Request $request){
		$validator = Validator::make($request->all(), [
			'ticket_id' => 'required',
			'message' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
		}else{
			$ticket = new Ticket();
			$getRecentTicket = Ticket::orderBy('ticket_id','desc')->first();
			if($getRecentTicket){
				$ticket->ticket_no = $getRecentTicket->ticket_no + 1;
			}else{
				$ticket->ticket_no = 500;
			}
			$ticket->parent_ticket_id = $request->ticket_id;

			$ticket->description = $request->message;
			$ticket->issue_category = $request->issue_category_id;
			$ticket->created_date = now();
			$ticket->created_by = auth()->user()->id;
			$ticket->save();

			return response()->json(['status' => 1, 'msg' => 'Ticket Reply Created Successfully.']);
		}
	}

	public function ticketReplyByCustomer(Request $request){
		$validator = Validator::make($request->all(), [
			'ticket_id' => 'required',
			'message' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
		}else{
			$ticket = new Ticket();
			$getRecentTicket = Ticket::orderBy('ticket_id','desc')->first();
			if($getRecentTicket){
				$ticket->ticket_no = $getRecentTicket->ticket_no + 1;
			}else{
				$ticket->ticket_no = 500;
			}
			$ticket->parent_ticket_id = $request->ticket_id;

			$ticket->description = $request->message;
			$ticket->issue_category = $request->issue_category_id;
			$ticket->created_date = now();
			$ticket->created_by = auth()->user()->id;
			$ticket->save();

			return response()->json(['status' => 1, 'msg' => 'Ticket Reply Created Successfully.']);
		}
	}

	public function issueCategoryIndex(){
		return view('admin.ticket-issue-category.index');
	}

	public function getIssueCategory(Request $request){

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
		$totalRecords = TicketIssueCategory::select('count(*) as allcount')->count();
		$totalRecordswithFilter = TicketIssueCategory::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

		/*Fetch records*/
		if ($request->length == -1) {
			$records = TicketIssueCategory::orderBy($columnName, $columnSortOrder)
			->where('ticket_issue_category.name', 'like', '%' . $searchValue . '%')
			->select('id', 'name','is_active')
			->take($rowperpage)
			->get();
		} else {
			$records = TicketIssueCategory::orderBy($columnName, $columnSortOrder)
			->where('ticket_issue_category.name', 'like', '%' . $searchValue . '%')
			->select('id', 'name','is_active')
			->skip($start)
			->take($rowperpage)
			->get();
		}

		$data_arr = array();

		$i = 1;
		foreach ($records as $record) {
			$id = $record->id;
			$name = $record->name;
			$is_active = $record->is_active;

			if ($is_active == 1) {
				$status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
			} else {
				$status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
			}

			$data_arr[] = array(
				"id" => $i,
				"name" => $name,
				"is_active" => $status,
				'action' => "<a class='edit_issue_category btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('ticket.issue.edit', $id) . "'>Edit</a>"
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

	public function issueCategoryStore(Request $request){
		$validator = Validator::make($request->all(), [
			'name' => 'required|unique:ticket_issue_category,name|max:255',
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
		}else{
			$issue = new TicketIssueCategory();
			$issue->name = $request->name;
			if ($request->is_active == 'on') {
				$issue->is_active = 1;
			} else {
				$issue->is_active = 0;
			}

			$issue->created_by = auth()->user()->id;
			$issue->save();

			return response()->json(['status' => 1, 'msg' => 'Issue Category Created Successfully.']);
		}
	}

	public function issueCategoryEdit($id){
		$issueCategory = TicketIssueCategory::where('id', $id)->first();
		return view('admin.ticket-issue-category.edit', compact('issueCategory'));
	}

	public function issueCategoryUpdate(Request $request){
		$validator = Validator::make($request->all(), [
			'id' => 'required',
			'name' => 'required|unique:ticket_issue_category,name,'.$request->id,
		]);

		if ($validator->fails()) {
			return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
		}else{
			$issue = TicketIssueCategory::find($request->id);
			$issue->name = $request->name;
			if ($request->is_active == 'on') {
				$issue->is_active = 1;
			} else {
				$issue->is_active = 0;
			}

			$issue->created_by = auth()->user()->id;
			$issue->save();

			return response()->json(['status' => 1, 'msg' => 'Issue Category Updated Successfully.']);
		}
	}
}