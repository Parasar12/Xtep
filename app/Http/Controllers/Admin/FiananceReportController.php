<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SalePaymentTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class FiananceReportController extends Controller
{

    public function index(){

        return view('admin.fianance_reports.index');
    }

    public function getFinanceReport(Request $request){


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

        $income_base_query = Order::where([
            ['fiscal_year_id',$fiscal_year],
            ['delivery_status','!=',8],
            ['delivery_status','!=',4]
        ]);
        $sales_payments = SalePaymentTrack::where('fiscal_year_id',$fiscal_year);
        $totalRecords = $income_base_query->count();
        $totalRecordswithFilter = $income_base_query->count();

        if ($request->start_date && $request->end_date){

            $records = $income_base_query->where([
                ['created_at','>=',$request->start_date],
                ['created_at','<=',$request->end_date]
            ])->take($rowperpage)
                ->get();
            $payments = $sales_payments->where([
                ['created_at','>=',$request->start_date],
                ['created_at','<=',$request->end_date]
            ])->take($rowperpage)
                ->get();
        }

       else if($request->year && $request->month){

            $year = $request->year;
            $month = $request->month;

            $date = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $start_date = "$year-$month-01";
            $end_date = "$year-$month-$date";

            $records = $income_base_query->where([
                ['created_at','>=',$start_date],
                ['created_at','<=',$end_date]
            ])->take($rowperpage)
                ->get();
            $payments = $sales_payments->where([
                ['created_at','>=',$start_date],
                ['created_at','<=',$end_date]
            ])->take($rowperpage)
                ->get();
        }

        else if ($request->length == -1) {
            $records = $income_base_query->where('created_at',today())->take($rowperpage)->get();
            $payments = $sales_payments->where([
                ['created_at',today()],
            ])->take($rowperpage)
                ->get();
        } else {
            $records = $income_base_query->where('created_at',today())->skip($start)->take($rowperpage)->get();
            $payments = $sales_payments->where([
                ['created_at',today()],
            ])->take($rowperpage)
                ->get();
        }

        $data_arr = array();
        $total_records = $records->count();
        $i = 1;
        $total_amount = 0;
        $total_logistic_payment = 0;
        foreach ($records as $record) {
            $total_amount += $record->net_total;
            $total_logistic_payment
                += $record->delivery_amount_by_vendor;
        }
        $total_paid_amount = 0;
        foreach ($payments  as $payment){
            $payment_status = $payment->order->delivery_status;
            if ($payment_status != 8)
                $total_paid_amount += $payment->paid_amount;
            if ($payment_status !=4)
                $total_paid_amount += $payment->paid_amount;
        }

        $total_receivable_amount = $total_amount - $total_paid_amount;
        $data_arr[] = array(
            "id" => $i,
            "total_sales_cost" =>'Rs.'. $total_amount,
            "total_received_amount" =>'Rs.' . $total_paid_amount,
            "total_receivable_amount" =>'Rs.'. $total_receivable_amount,
            "total_payable_amount_to_logistic" =>'Rs.'. $total_logistic_payment
        );

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            'total_amount'=>'Rs. '.$total_amount,
            'total_records'=>$total_records
        );

        return Response::json($response);
    }


    public function getPaymentReport(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "",
            "result" => ''
        );
        try {
            $fiscal_year = getFiscalYear();

            $income_base_query = Order::where('fiscal_year_id',$fiscal_year);
            $sales_payments = SalePaymentTrack::where('fiscal_year_id',$fiscal_year);

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

                    $data = [];
                    $labels = [];
                    $colors = [];


                $labels  = ['total_sales_cost','total_received_amount','total_receivable_amount','total_payable_to_logistic_vendor'];
                $colors = ['#0997a0','#8887b0','#3333a0','#4444d0'];
                 $records = $income_base_query->where([
                     ['created_at','>=',$start_date],
                     ['created_at','<=',$end_date]
                 ])->get();

                $payments = $sales_payments->where([
                    ['created_at','>=',$start_date],
                    ['created_at','<=',$end_date]
                ])->get();

                $total_amount =0;
                $total_logistic_payment = 0;
                foreach ($records as $record) {
                    $total_amount += $record->net_total;
                    $total_logistic_payment
                        += $record->delivery_amount_by_vendor;
                }
                $total_paid_amount = 0;
                foreach ($payments  as $payment){
                    $total_paid_amount += $payment->paid_amount;
                }

                $total_receivable_amount = $total_amount - $total_paid_amount;

               $data = [$total_amount,$total_paid_amount,$total_receivable_amount,$total_logistic_payment];

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
