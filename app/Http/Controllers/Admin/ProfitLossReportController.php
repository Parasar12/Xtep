<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProfitLossReportController extends Controller
{
    public function index(){

        return view('admin.profit_loss_reports.index');
    }

    public function getProfitLossData(Request $request){


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

        $fiscal_year = getFiscalYear();

        $income_base_query = Order::leftJoin('sales_profit_loss','sales_profit_loss.order_id','orders.id')->where([
            ['orders.fiscal_year_id',$fiscal_year],
            ['orders.delivery_status','!=',8],
            ['orders.delivery_status','!=',4]
        ]);
        $totalRecords = $income_base_query->count();
        $totalRecordswithFilter = $income_base_query->count();

        if ($request->start_date && $request->end_date){

            $records = $income_base_query->where([
                ['orders.created_at','>=',$request->start_date],
                ['orders.created_at','<=',$request->end_date]
            ])->take($rowperpage)
                ->get();
        }

        if($request->year && $request->month){

            $year = $request->year;
            $month = $request->month;

            $date = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $start_date = "$year-$month-01";
            $end_date = "$year-$month-$date";
            $records = $income_base_query->where([
                ['orders.created_at','>=',$start_date],
                ['orders.created_at','<=',$end_date]
            ])->take($rowperpage)
                ->get();
        }

        if ($request->length == -1) {
            $records = $income_base_query->take($rowperpage)->get();
        } else {
            $records = $income_base_query->skip($start)->take($rowperpage)->get();
        }

        $data_arr = array();
        $total_records = $records->count();
        $i = 1;
        $total_profit = 0;
        $total_loss = 0;
        foreach ($records as $record) {

            if (($record->net_amount) > 0)
                $total_profit += floatval($record->net_amount);

            if (($record->net_amount < 0))
                   $total_loss += floatval($record->net_amount);

            $fiscal_year = getFiscalYearName($record->fiscal_year_id);
            $code = $record->code;
            $net_total = $record->net_total;
            $profit = ($record->net_amount) > 0  ? 'Rs .' . $record->net_amount : '---';
            $loss = ($record->net_amount) < 0  ?  'Rs .' . $record->net_amount : '---';
            $created_at = date('Y-m-d',strtotime($record->created_at));

            $data_arr[] = array(
                "id" => $i,
                "fiscal_year_id" => $fiscal_year,
                "code" => '#'.$code,
                "net_total" => "Rs. $profit",
                "profit" =>  $profit,
                "loss" =>  $loss,
                "created_at" => $created_at,
            );
            $i++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            'total_profit'=>'Rs. '.$total_profit,
            'total_loss'=>'Rs. '.$total_loss,
            'total_records'=>$total_records
        );

        return Response::json($response);
    }


    public function statusReport(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "",
            "result" => ''
        );
        try {
            if ($request->year) {
                $year = $request->year;
                $month = $request->month;
                $date = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $start_date = "$year-$month-01";
                $end_date = "$year-$month-$date";
            } else {
                $start_date = $request->start_date;
                $end_date = $request->end_date;
            }

            $status = DB::table('order_status')
                ->get();

            $data = [];
            $labels = [];
            $colors = [];

            foreach ($status as $val) {
                $labels [] = $val->name;
                $colors[] = $this->rand_color();
                $data[] = Order::where([
                    ['created_at','>=',$start_date],
                    ['created_at','<=',$end_date],
                    ['delivery_status', $val->id],
                    ['fiscal_year_id',getFiscalYear()]
                ])
                    ->count('delivery_status');

            }

            $data[] = 0;

            $output['response'] = true;
            $output['result'] = [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors
            ];
        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }
}
