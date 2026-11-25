<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index()
    {
        $this->_hasMenuAccess('faq-index');

        return view('admin.faq.index');
    }

    public function getFaq(Request $request)
    {
        $this->_hasMenuAccess('faq-index');

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
        $totalRecords = Faq::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Faq::select('count(*) as allcount')->where('question', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Faq::orderBy($columnName, $columnSortOrder)
                ->where('question', 'like', '%' . $searchValue . '%')
                ->select('id', 'question', 'is_active')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Faq::orderBy($columnName, $columnSortOrder)
                ->where('question', 'like', '%' . $searchValue . '%')
                ->select('id', 'question', 'is_active')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $question = $record->question;
            $is_active = $record->is_active;

            if ($is_active == 1) {
                $status = '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
            } else {
                $status = '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
            }

            $data_arr[] = array(
                "id" => $i,
                "question" => $question,
                "is_active" => $status,
                'action' => "<a class='edit_brand btn btn-info btn-sm btn-rounded waves-effect waves-light' onclick='edit($id)'>Edit</a>"
            );
            $i++;
        }


        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return Response::json($response);
    }

    public function add(): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $view = view('admin.faq.add')->render();
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $view,
            );

        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function store(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'question' => ['required'],
                'answer' => ['required']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                Faq::create([
                    'question' => $request->question,
                    'answer' => $request->answer,
                    'is_active' => true,
                ]);
                $output = array(
                    'response' => true,
                    'msg' => "FAQ SAVE Successfully",
                    'result' => "",
                );
            }
        } catch (Exception  $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function edit(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $faq = Faq::find($request->id);
            $view = view('admin.faq.edit', compact('faq'))->render();
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $view,
            );

        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function update(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'question' => ['required'],
                'answer' => ['required']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                $faq = Faq::find($request->id);
                $faq->question = $request->question;
                $faq->answer = $request->answer;
                $faq->is_active = (bool)$request->status;
                $faq->save();

                $output = array(
                    'response' => true,
                    'msg' => "FAQ SAVE Successfully",
                    'result' => "",
                );
            }
        } catch (Exception  $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

}
