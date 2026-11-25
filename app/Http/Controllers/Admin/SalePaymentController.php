<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogistiPaymentTrack;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\SalePaymentTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SalePaymentController extends Controller
{

    public function index(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {
            $order = Order::where('id',$request->id)->first();
            $payments = SalePaymentTrack::where('sale_entry_id',$order->id)->get();

            $total_paid_amount = 0 ;
            foreach ($payments as $payment){
                $total_paid_amount += $payment->paid_amount;
            }
            $remain_paid_amount = intval($order->net_total) - intval($total_paid_amount);
            $paymnent_methods = PaymentMethod::where('status',true)->get();
            $output['response'] = true;
            $output['msg'] = true;
            $output['result'] = view('admin.orders.paymentView', compact('order','total_paid_amount','remain_paid_amount','paymnent_methods','payments'))->render();
        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function sales_payment_store(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {

            $validator = Validator::make($request->all(), [
                'paid_amount' => ['required','gt:0'],
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }
            DB::beginTransaction();
            $order = Order::findOrFail($request->sale_id);
            if (($request->paid_amount )>( $request->due_amount)) {
                $output['result'] = [
                    'paid_amount' => 'Received Amount Cannot be  more  than Due Amount'
                ];
                throw new \Exception("Form Validation Error");
            }
            SalePaymentTrack::create([
                'sale_entry_id'=>$request->sale_id,
                'payment_method_id'=>$request->payment_method_id,
                'paid_amount'=>$request->paid_amount,
                'created_by'=>auth()->user()->id,
                'fiscal_year_id'=>getFiscalYear()
            ]);

            if($request->logistic_paid_amount){
                if (($request->logistic_paid_amount )>( $request->logistic_due_amount)) {
                    $output['result'] = [
                        'logistic_paid_amount' => 'Received Amount Cannot be  more  than Due Amount'
                    ];
                    throw new \Exception("Form Validation Error");
                }

                DB::table('logistic_payment_track')->insert([
                    'order_id'=>$request->sale_id,
                    'payment_method_id'=>$request->logistic_payment_method_id,
                    'paid_amount'=>$request->logistic_paid_amount,
                    'created_by'=>auth()->user()->id,
                    'fiscal_year_id'=>getFiscalYear(),
                    'vendor_id'=>$order->delivery_vendor_id
                ]);
            }

            DB::commit();
            $output['response'] = true;
            $output['msg'] = 'payment added successfully';
        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function paymentForLogistic(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {


            $order = Order::where('id',$request->id)->first();
            $logistic_payments = LogistiPaymentTrack::where('order_id',$order->id)->get();

            $logistic_total_paid_amount = 0 ;
            foreach ($logistic_payments as $payment){
                $logistic_total_paid_amount += $payment->paid_amount;
            }
            $logistic_remain_paid_amount = intval($order->delivery_amount_by_vendor) - intval($logistic_total_paid_amount);
            $payment_methods = PaymentMethod::where('status',true)->get();

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.logistic_payment_view', compact('order','logistic_total_paid_amount','logistic_remain_paid_amount','payment_methods','logistic_payments'))->render();


        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function paymentForLogisticSave(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {

            $validator = Validator::make($request->all(), [
                'logistic_paid_amount' => ['required','gt:0'],
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

               $order = Order::findOrFail($request->order_id);

                if (($request->logistic_paid_amount )>( $request->logistic_due_amount)) {
                    $output['result'] = [
                        'logistic_paid_amount' => 'Received Amount Cannot be  more  than Due Amount'
                    ];
                    throw new \Exception("Form Validation Error");
                }

                DB::table('logistic_payment_track')->insert([
                    'order_id'=>$request->order_id,
                    'payment_method_id'=>$request->logistic_payment_method_id,
                    'paid_amount'=>$request->logistic_paid_amount,
                    'created_by'=>auth()->user()->id,
                    'fiscal_year_id'=>getFiscalYear(),
                    'vendor_id'=>$order->delivery_vendor_id
                ]);

            $output['response'] = true;
            $output['msg'] = 'payment added successfully';
        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }
}
