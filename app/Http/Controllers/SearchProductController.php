<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class SearchProductController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('visitor_ip_address');

        return view('admin.customer_search_product.index');
    }

    public function getCustomerSearchProduct(Request $request): JsonResponse
    {
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
        $totalRecords =  DB::table('search_record')->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table('search_record')->select('count(*) as allcount')->where('searched_product', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = DB::table('search_record')->orderBy($columnName, $columnSortOrder)
                ->where('searched_product', 'like', '%' . $searchValue . '%')

                ->take($rowperpage)
                ->get();
        } else {
            $records = DB::table('search_record')->orderBy($columnName, $columnSortOrder)
                ->where('searched_product', 'like', '%' . $searchValue . '%')
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = $start + 1;
        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $i,
                "searched_product" => $record->searched_product,
                "ip_address" => $record->ip_address,
                'time'=>$record->time,
                'checkbox' => "<input type='checkbox'  id='multiple_delete-$record->id' class='multiple_delete' name='multiple_delete' onchange='arrayProducts($record->id,this)' value='" . $record->id . "'>",
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

    public function delete(Request $request)
    {

        $output = array(
            'response' => false,
            'msg' => "something went wrong",
            'result' => "",
        );

        try {
            for ($i = 0; $i < count($request->id); $i++) {
                DB::table('search_record')->where('id', $request->id[$i])->delete();
            }

            $output = array(
                'response' => True,
                'msg' => "Searched Record Delete Successfully",
                'result' => "",
            );

        }catch (\Exception $exception){
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

}
