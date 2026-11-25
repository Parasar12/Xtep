<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;

class IpTrackController extends Controller
{
    public function index()
    {
        $this->_hasMenuAccess('visitor_ip_address');

        return view('admin.ip_track.index');
    }

    public function getIpAddress(Request $request): JsonResponse
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
//        $totalRecordswithFilter = DB::table('visitor_ip')->select('count(*) as allcount')->where('ip_address', 'like', '%' . $searchValue . '%')->count();

//        $query =DB::table('visitor_ip')
//                ->orderBy($columnName, $columnSortOrder)
//                ->where('ip_address', 'like', '%' . $searchValue . '%')
//                ->orWhere('country_name', 'like', '%' . $searchValue . '%')
//                ->orWhere('region_name', 'like', '%' . $searchValue . '%')
//                ->orWhere('city_name', 'like', '%' . $searchValue . '%')
//                ->groupBy('ip_address')
//            ->get();

//        dd($query);
        /*Fetch records*/
//        if ($request->length == -1) {
//
//            $records = $query->take($rowperpage);
//        } else {
//
//            $records =  $query->skip($start)->take($rowperpage);
//        }
//
//        $totalRecordswithFilter = $records->count();
//         $totalRecords =$query->count();


//         = \

        $query = DB::table('visitor_ip')->orderBy($columnName, $columnSortOrder)
            ->orWhere('country_name', 'like', '%' . $searchValue . '%')
            ->orWhere('city_name', 'like', '%' . $searchValue . '%')
            ->orWhere('region_name', 'like', '%' . $searchValue . '%')
            ->groupBy('ip_address');

        $totalRecords = $query->get()->count();
        $totalRecordswithFilter =$query->get()->count();;
        /*Fetch records*/
        if ($request->length == -1) {
            $records =
                $query
                ->take($rowperpage)
                ->get();
        } else {
            $records = $query
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }



        $data_arr = array();

        $i = $start + 1;
        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $i,
                "ip_address" => $record->ip_address,
                'date_time' => $record->date_time,
                "country_name" => $record->country_name,
                "region_name" => $record->region_name,
                "city_name" => $record ? $record->city_name : '',
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
                DB::table('visitor_ip')->where('id', $request->id[$i])->delete();
            }

            $output = array(
                'response' => True,
                'msg' => "Ip Address Delete Successfully",
                'result' => "",
            );

        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

}
