<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\ProductMaster;
use App\Models\Province;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('sales_entry');

        return view('admin.sales.index');
    }

    public function getSales(Request $request): JsonResponse
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



        $query = DB::table('sales_master')->orderBy($columnName, $columnSortOrder)
            ->join('users','users.id','sales_master.customer_id')
            ->select('sales_master.*','users.name as customer_name')
            ->orWhere('users.name', 'like', '%' . $searchValue . '%');

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
                "bill_number" => $record->bill_number,
                'customer_id' => $record->customer_name,
                "bill_date" => $record->bill_date,
                "total_amount" => $record->total_amount,
                "action" => "<a class='view_sale btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='#'  onclick='viewSales($record->id)'>View</a> &nbsp;&nbsp;<a class='btn btn-warning btn-sm btn-rounded waves-effect waves-light'  onclick='paymentSale($record->id)' href='javascript:'>Pay</a>"

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

    public function  add(){
        $shippings = ShippingMethod::where('is_active',true)->get();
        $delivery_vendors = DB::table('vendors')->get();
        $order_status = OrderStatus::all();
        $provinces = Province::all();
        $orderModes = DB::table('order_modes')->get();
        $users = User::where('is_admin',true)->get();
        return view('admin.sales.add',compact('shippings','orderModes','delivery_vendors','order_status','provinces','users'));
    }

    public function store(Request $request): JsonResponse
    {
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {

            $validator = Validator::make($request->all(), [
                "customer" => ['required', 'string'],
                'vat' => ['required', 'numeric'],
//                'bill_date' => ['required'],
                'particulars' => ['required', 'array'],
                'quantity' => ['required', 'array'],
                'unitPrice' => ['required', 'array'],
                "totalPrice" => ['required', 'array'],
                'term_amount' => ['required', 'numeric'],
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }
            DB::beginTransaction();

            $customer = $request->post('customer');

            // check customer exists
            if (!User::where('id', $customer)->exists()) {
                $vendor = User::create([
                    'name' => $request->customer,
                    'created_at' => now(),
                ]);
                $customer = $vendor->id;
            }
            // calculate gross price
            $grossAmount = 0;
            foreach ($request->totalPrice as $price) {
                $grossAmount += floatval($price);
            }

            if ($request->discountType == 'percent') {
                $discount_amt = $grossAmount * (floatval($request->discount_per) / 100);
            } else {
                $discount_amt = floatval($request->discount_amt);
            }

            $grossAmountAfterDiscount = $grossAmount - floatval($request->discount_amt);
            $taxAmount = $grossAmount * ($request->vat / 100);
            $netAmount = $grossAmountAfterDiscount + $taxAmount;

            $bill_date = date('Y-m-d',strtotime($request->bill_date));
            $due_date = date('Y-m-d',strtotime($request->due_date));
            $total_coupon_discount = 0;
            for ($i = 0; $i < sizeof($request->particulars); $i++) {
                $total_coupon_discount += $request->couponDiscount[$i];
            }
            if($request->shipping_method){
                $netAmount += getShipingPrice($request->shipping_method);
            }
            $delivery_charge = $request->delivery_vendor_amount_to_customer;
            // if($request->delivery_vendor){
                $netAmount += $delivery_charge;
            // }

            $sale = new Order();
            $sale->code = $sale->max('code') + 1;
            $sale->user_id = $customer;
            $sale->delivery_vendor_id = $request->delivery_vendor;
            $sale->vat = $request->vat;
            $sale->grand_total = $grossAmount;
            $sale->shipping_address_id = $request->shipping_address_id;
            $sale->discount_amount = $discount_amt;
            $sale->shipping_method_id = $request->post('shipping_method') ?? '';
            $sale->tax_amount = $taxAmount;
            $sale->net_total = $netAmount;
            $sale->delivery_status = $request->order_status;
            $sale->created_by = \auth()->user()->id;
            $sale->mode_type = $request->mode_type_id;
            $sale->discount_per = $request->discount_per;
            $sale->discountType = $request->discountType;
            $sale->delivery_vendor_amount = $delivery_charge;
            $sale->total_coupon_discount = $total_coupon_discount;
            $sale->fiscal_year_id = getFiscalYear();
            $sale->delivery_amount_by_vendor = $request->delivery_vendor_amount;
            $sale->order_by = $request->order_by;
            $sale->date = $request->estimated_date;
            $sale->customer_remarks = $request->customer_remarks;
            $sale->save();
//            $product_profit = 0;
            for ($i = 0; $i < sizeof($request->particulars); $i++) {
                $productId = $request->particulars[$i];
                $attributeId = ($request->variants[$i]);

//                $product = ProductMaster::where([
//                    ['product_id',$productId],
//                    ['attribute_id',$attributeId],
//                    ['is_active',true]
//                ])->latest()->first();
//                $profitMargin = $product ? $product->profit_margin : 0;
//                $product_profit += ($profitMargin * $request->post('quantity')[$i]);


                OrderDetail::create([
                    'order_id' => $sale->id,
                    'product_id' => $productId,
                    'attribute'=>$attributeId,
                    'code' => $sale->code,
                    'quantity'=>$request->post('quantity')[$i],
                    'price' => $request->unitPrice[$i],
                    'delivery_status'=>$request->order_status,
                    'product_coupon_discount'=>$request->couponDiscount[$i]
//                    'total_amount' => $request->totalPrice[$i],
                ]);
            }
//            $net_profit = $product_profit + $delivery_charge - $request->delivery_vendor_amount;
//
//            DB::table('sales_profit_loss')->insert([
//                'order_id'=>$sale->id,
//                'net_amount'=>$net_profit
//            ]);

            $output['response'] = true;
            $output['msg'] = 'Order Process Added Successfully';

            DB::commit();

        } catch (\Exception $exception) {

            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }


    public function view(Request $request){
        $output = [
            'response'=>false,
            'msg'=>'something went wrong',
            'result'=>''
        ];

        try {

//            $output['response'] = true;
//            $output['result']= view('')
        }catch (\Exception $exception){
            $output['msg'] = $exception->getMessage();
        }
    }

    public function getSalesPaymentView(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {
            $sale = Sale::where('id',$request->sale_entry_id)->first();
            $payments = DB::table('sales_payments')->where('sale_entry_id',$sale->sale_entry_id)->get();
            $total_paid_amount = 0 ;
            foreach ($payments as $payment){
                $total_paid_amount += $payment->paid_amount;
            }
            $remain_paid_amount = intval($sale->total_amount) - intval($total_paid_amount);
            $paymnent_methods = DB::table('payment_method')->where('status',true)->get();
            $output['response'] = true;
            $output['msg'] = true;
            $output['result'] = view('sales.paymentView', compact('sale','total_paid_amount','remain_paid_amount','paymnent_methods'))->render();
        } catch (Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    public function payment_store(Request $request){
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {

            $validator = Validator::make($request->all(), [
                'paid_amount' => ['required'],
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            if (($request->paid_amount )>( $request->due_amount)) {
                $output['result'] = [
                    'paid_amount' => 'Received Amount Cannot be  more  than Due Amount'
                ];
                throw new \Exception("Form Validation Error");
            }
            DB::table('sales_payments')->insert([
                'sale_'=>$request->sale_id,
                'paid_amount'=>$request->paid_amount,
                'payment_method_id'=>$request->payment_method_id,
                'fiscal_year_id'=>getFiscalYear()
            ]);
            $output['response'] = true;
            $output['msg'] = 'payment added successfully';
        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }
        return Response::json($output);
    }

    private static function generateUniqueQuotationNumber(): string
    {
        do {
            $lastPatient = Sale::orderBy('id', 'DESC')->first();
            if (!empty($lastPatient)) {
                $number = $lastPatient->id + 1;
            } else {
                $number = 1;
            }
            $code = "SE-".date('Ymd').'-' . $number;
        } while (Sale::where('bill_number', $code)->first());
        return $code;
    }

    public function getShippingAmount(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];
        try {
             $data = ShippingMethod::where('id',$request->ship_id)->first()->cost;
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $data;

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function getDeliveryVendorCharge(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];
        try {
             $data = DB::table('delivery_vendors')->where('id',$request->vendor_id)->first()->delivery_charge;
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $data;

        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

}