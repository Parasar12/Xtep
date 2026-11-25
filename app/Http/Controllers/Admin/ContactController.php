<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contact.index');
    }

    public function getList(Request $request): JsonResponse
    {
        $length = $request->post('length');
        $start = $request->post('start');
        $columns = $request->post('columns');
        $order = $request->post('order');

        $orderBySql = "ORDER BY " . $columns[$order[0]['column']]['data'] . " " . $order[0]['dir'];
        $whereAllSql = "WHERE 1 ";
        foreach ($columns as $key => $value) {
            if (!empty($request->post('search')['value']) && 'action' != $value['data']) {
                $init = (0 == $key) ? 'AND ' : 'OR ';

                $sqlString = $init . $value['data'] . " LIKE '%" . $request->post('search')['value'] . "%' ";
                $whereAllSql .= $sqlString;
            }
        }

        $basicQuery = Contact::select('id', 'name', 'email', 'created_at')->orderBy('created_at', 'desc')->toSql();


        if ($request->length == -1) {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql";
        } else {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql LIMIT $start, $length";
        }

        $queryResult = DB::select($dataQuery);

        $result = array();
        $count = 1;
        foreach ($queryResult as $value) {
            $result[] = array(
                'id' => $count,
                "name" => $value->name,
                'email' => $value->email,
                "created_at" => $value->created_at,
                'action' => " <button type='button' class='btn btn-primary btn-sm btn-rounded waves-effect waves-light' onclick='viewMessage($value->id)' '>View</button>"
            );
            $count++;
        }

        $recordsFilteredQuery = "SELECT count(id) AS records_filtered FROM ($basicQuery) a $whereAllSql";
        $recordsFiltered = DB::select($recordsFilteredQuery);
        $recordsFiltered = $recordsFiltered[0]->records_filtered;

        $response = array(
            'draw' => intval($request->post('draw')) + 1,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        );

        return Response::json($response);
    }

    public function view(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $message = Contact::find($request->id);
            $view = view('admin.contact.view', compact('message'))->render();
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
}
