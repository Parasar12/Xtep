<?php

namespace App\Http\Controllers;

use App\Mail\OrderCancelAdmin;
use App\Mail\OrderStatusToCustomer;
use App\Models\Address;
use App\Models\Attribute;
use App\Models\Coupon;
use App\Models\Expense;
use App\Models\LogistiPaymentTrack;
use App\Models\PaymentMethod;
use App\Models\ProductAttribute;
use App\Models\ProductMaster;
use App\Models\Province;
use App\Models\SalePaymentTrack;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\OrderLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('SalesManagement-Orders');
        $fiscalYear = DB::table('fiscal_year_master')->get();
        return view('admin.orders.index', compact('fiscalYear'));
    }

    public function getOrders(Request $request, $status)
    {
        $this->_hasMenuAccess('SalesManagement-Orders');

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

        $status = DB::table('order_status')->where('name', $request->status)->first();
        //        dd($status);
        /*Total records*/
        $totalRecords = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.delivery_status', $status->id ?? '')
            ->select('count(*) as allcount')->count();

        $totalRecordswithFilter = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.delivery_status', $status->id ?? '')
            ->select('count(*) as allcount')->where('users.name', 'like', '%' . $searchValue . '%')->count();

        $fiscal_year = getFiscalYear();

        $base_query = Order::leftJoin('users', 'orders.user_id', '=', 'users.id')->orderBy($columnName, $columnSortOrder)
            ->leftJoin('addresses', 'orders.shipping_address_id', 'addresses.id')->orderBy($columnName, $columnSortOrder)
            ->leftJoin('sales_profit_loss', 'sales_profit_loss.order_id', 'orders.id')
            ->leftJoin('vendors', 'vendors.id', 'orders.delivery_vendor_id')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->where('orders.delivery_status', $status->id ?? '')
            // ->where('orders.fiscal_year_id', $fiscal_year)
            ->select('orders.id as order_id', 'orders.fiscal_year_id', 'orders.net_total', 'orders.delivery_status', 'orders.fiscal_year_id', 'orders.code', 'orders.order_by', 'users.name', 'users.contact', 'addresses.*', 'orders.shipping_method_id', 'orders.mode_type', 'sales_profit_loss.net_amount', 'users.id as user_id', 'vendors.id as vendor_id', 'orders.logistic_order_code', 'orders.logistic_token', 'orders.is_settled');
        /*Fetch records*/
        //        dd($request->all());
        if ($request->fiscal_year) {
            $records =
                $base_query
                    ->where('orders.fiscal_year_id', $request->fiscal_year)
                    ->take($rowperpage)
                    ->get();
        }
        if ($request->start_date && $request->end_date) {

            $records = $base_query->where([
                ['orders.date', '>=', $request->start_date],
                ['orders.date', '<=', $request->end_date]
            ])->take($rowperpage)
                ->get();
        }

        if ($request->year && $request->month) {

            $year = $request->year;
            $month = $request->month;

            $date = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $start_date = "$year-$month-01";
            $end_date = "$year-$month-$date";
            $records = $base_query->where([
                ['orders.date', '>=', $start_date],
                ['orders.date', '<=', $end_date]
            ])->take($rowperpage)
                ->get();
        }
        $total_amount = $base_query->sum('net_total');
        if ($request->length == -1) {
            $records = $base_query->take($rowperpage)->get();
        } else {
            $records = $base_query->skip($start)->take($rowperpage)->get();
        }
        $data_arr = array();
        $total_records = $records->count();
        $i = $start + 1;


        foreach ($records as $record) {
            $id = $record->order_id;
            $fiscal_year = getFiscalYearName($record->fiscal_year_id);
            $code = $record->code;
            $name = $record->name;
            $contact = $record->contact;
            $order_by = $record->orderBy->name ?? "";
            $mode = getModeName($record->mode_type);
            $billing_province = ($record->province_id) ? getProviceName($record->province_id) : '-';
            $billing_district = ($record->district_id) ? getDistrictName($record->district_id) : '-';
            $billing_addresses = ($record->street) ? $record->street : '-';
            $order_status = getOrderStatus($record->delivery_status);
            $net_total = $record->net_total;
            $net_amount = $record->net_amount ?? 0;

            $paymentSettlement = "<a class=' btn btn-success btn-sm btn-rounded waves-effect waves-light' href='" . route('view_sale', base64_encode($record->code)) . "' style='color:white' >View</a>";
            $expense = Expense::where([
                ['order_id', $record->order_id],
                ['status', true]
            ])->first();

            $customerName = "<a onclick='viewCustomerProfile($record->order_id)' style='cursor: pointer;color: #0d6efd'>" . $name . "</a>";
            if ($record->delivery_status == '8' && !($record->is_settled))
                $paymentSettlement .= " <a class=' btn btn-warning btn-sm btn-rounded waves-effect waves-light' onclick='paymentSettlement($record->order_id)' style='color:white' >Payment Settlement</a>";

            if ($record->delivery_status != '8')
                $paymentSettlement .= "<a class=' btn btn-info btn-sm btn-rounded waves-effect waves-light m-1' onclick='changeOrderStatus($id)' style='color:white' >Change Status</a>
                             <a class=' btn btn-primary btn-sm btn-rounded waves-effect waves-light' onclick='paymentForSale($id)' style='color:white' >Pay</a>
                             <a class=' btn btn-success btn-sm btn-rounded waves-effect waves-light' onclick='paymentForLogistic($id)' style='color:white' >Pay To Logistic</a>
                              <a class=' btn btn-secondary btn-sm btn-rounded waves-effect waves-light' onclick='printOrder($id)' style='color:white' ><i class='fa fa-print'></i></a>";

            if ($record->delivery_status != '8' && $record->logistic_token && $record->logistic_order_code)
                $paymentSettlement .= " <a class=' btn btn-danger btn-sm btn-rounded waves-effect waves-light' onclick='viewOrderLog($record->vendor_id,$record->logistic_order_code,`$record->logistic_token`,$record->user_id)' style='color:white' >Logistic Vendor Order Log</a>
                              ";

            if ($record->delivery_status == '1')
                $paymentSettlement .= "<a class=' btn btn-success btn-sm btn-rounded waves-effect waves-light' href='" . route('orders.edit', $id) . "'  style='color:white' >Edit</i></a>";
            //            <a class='view_order btn btn-primary btn-sm btn-rounded waves-effect waves-light' style='color:white' onclick='showOrderDetailModal($code)'>View</a>
            $data_arr[] = array(
                "id" => $i,
                "fiscal_year_id" => $fiscal_year,
                "code" => $code,
                "name" => $customerName,
                "contact" => $contact,
                //                "delivery_status" => $order_status,
                //                "shipping_address" => $billing_province . ' , ' . $billing_district . ' , ' . $billing_addresses,
                //                "net_total" =>$net_total,
                //                "net_amount" =>$net_amount,
                //                "mode_type" => $mode,
                //                'order_by'=>$order_by,
                'action' => $paymentSettlement
            );
            $i++;
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            'total_amount' => 'Rs. ' . $total_amount,
            'total_records' => $total_records
        );

        return Response::json($response);
    }

    public function showOrderDetails($code)
    {
        $order = Order::where('code', $code)->first();

        $status = OrderStatus::all();
        return view('frontend.order.order_details', compact('order', 'status'));
    }

    public function changeOrderStatus(Request $request)
    {
        $this->_hasMenuAccess('SalesManagement-Orders');

        DB::beginTransaction();
        OrderDetail::where('id', $request->id)->update([
            'delivery_status' => $request->status
        ]);
        $orderDetail = OrderDetail::where('id', $request->id)->first();

        \Mail::to($orderDetail->order->user->email)->send(new \App\Mail\OrderStatus($orderDetail, $orderDetail->order->user->name));

        DB::commit();

        return response()->json(['status' => 1, 'msg' => "Order Status Updated Successfully"]);
    }

    public function store(Request $request)
    {

        dd($request->all());
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $request->validate([
                'province_id' => 'required|numeric',
                'district_id' => 'required|numeric',
                'street' => 'required',
                'shipping' => 'required',
                'payment_mode' => 'required',
            ]);

            DB::beginTransaction();
            $address_query = Address::where([
                ['user_id', auth()->user()->id],
                ['province_id', $request->province_id],
                ['district_id', $request->district_id],
                ['street', $request->street]
            ]);
            if (!$address_query->exists()) {
                $address = Address::create([
                    'user_id' => Auth::user()->id,
                    'district_id' => $request->district_id,
                    'province_id' => $request->province_id,
                    'street' => $request->street,
                ]);
            } else {
                $address = $address_query->orderBy('created_at', 'desc')->first();
            }
            $lastOrderDetail = OrderDetail::orderBy('created_at', 'desc')->first();
            $code = ($lastOrderDetail) ? $lastOrderDetail->code + 1 : 100000;

            $orderData = Order::max('id');
            $orderId = $orderData + 1;
            $coupon = [];
            if ($request->coupon_id)
                $coupon = Coupon::where('id', $request->coupon_id)->first();

            $user = User::where('is_admin', true)->first();
            /*Order Master*/
            $order = new Order();
            $order->id = $orderId;
            $order->user_id = auth()->user()->id;
            $order->billing_address_id = $address->id;
            $order->shipping_address_id = $address->id;
            $order->payment_type = $request->payment_mode;
            $order->shipping_method_id = $request->shipping;
            $order->total_coupon_discount = $request->total_coupon_discount;
            $order->code = $code;
            $order->grand_total = $request->sub_total;
            $order->net_total = $request->total_amount;
            $order->fiscal_year_id = getFiscalYear();
            $order->coupon_code = $request->coupon_id ? $coupon->code : '';
            $order->mode_type = '1';
            $order->order_by = $user->id;
            $order->save();


            /*Order Log*/
            $log = new OrderLog;
            $log->code = $code;
            $log->log = 'Thank you for the Order. Order is getting Processed';
            $log->save();

            /*Order Detail*/
            for ($i = 0; $i < sizeof($request->product_id); $i++) {

                $details = new OrderDetail();
                $details->order_id = $order->id;
                $details->product_id = $request->product_id[$i];
                $details->price = $request->price[$i];
                $details->quantity = $request->quantity[$i];
                $details->code = $code;
                $details->product_coupon_discount = $request->product_coupon_discount[$i];
                $details->delivery_status = 1;
                $details->payment_status = 1;
                $details->attribute = isset($request->attribute[$i]) ? $request->attribute[$i] : '';
                $details->save();
            }

            if ($request->total_coupon_discount > 0) {

                DB::table('order_coupon')->insert([
                    'coupon_id' => $request->coupon_id,
                    'user_id' => auth()->user()->id,
                    'order_id' => $orderId
                ]);
            }

            $this->clearQuickOrderData();
            DB::table('quick_orders')->where('user_id', auth()->user()->id)->delete();
            Cart::where('user_id', auth()->user()->id)->delete();
            $response = messageInAllPlatForm($order->id);
            //            if($response['response'] == false){
            //                $output['response'] = $response['response'];
            //                $output['msg'] = $response['msg'];
            //            }else{
            DB::commit();
            $output['response'] = true;
            $output['result'] = route('orders.confirm', $code);

            //            }

        } catch (Exception $e) {
            DB::rollBack();
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }


    public function show($code)
    {

        $order = Order::where('code', $code)->first();
        $status = OrderStatus::all();
        if ($order) {
            return view('admin.orders.details', compact('order', 'status'));
        } else {
            return redirect('orders')->with('status', 'Order Not Found');
        }
    }

    public function orderConfirm($code)
    {
        $status = OrderStatus::all();

        $order = Order::where('code', $code)
            ->leftJoin('payment_mode', 'orders.payment_type', '=', 'payment_mode.payment_mode_id')
            ->first();
        Session::forget('message');
        Session::put('message', 'ordered successfully !!');
        return view('frontend.cart.order_confirm', compact('order', 'status'));
    }

    public function cancelOrder(Request $request): JsonResponse
    {

        $output = [
            'result' => '',
            'response' => false,
            'msg' => ''
        ];
        try {
            $id = $request->id;
            $orderItem = OrderDetail::find($id);

            $order = Order::find($orderItem->order_id);


            DB::beginTransaction();
            $orderItem->delivery_status = 8;
            $orderItem->save();

            $details = $order->orderDetails()->get();
            $items = $details->where('delivery_status', '!=', 8)->count();
            if ($items == 0) {
                $order->delivery_status = 8;
                $order->save();
            }
            DB::table('order_cancellations')->insert([
                'order_id' => $orderItem->order_id,
                'reason' => $request->reason
            ]);

            $dateOrders = Order::where([
                ['user_id', auth()->id()],
                ['created_at', \request()->order_date]
            ])->get();
            $admins = User::where('is_admin', 1)->get();
            $user = User::where('id', \auth()->user()->id)->first();
            foreach ($admins as $admin) {
                \Mail::to($admin->email)->send(new OrderCancelAdmin($orderItem, $user));
            }
            // dd($dateOrders);
            DB::commit();
            $view = view('frontend.orders.order_details', compact('dateOrders'))->render();

            $output['msg'] = 'Order Status Updated Successfully';
            $output['response'] = true;
            $output['result'] = $view;
        } catch (Exception $e) {
            DB::rollBack();
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function check_coupon_code(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $base_query = Coupon::where('code', $request->get('coupon_code'));
            $coupon = $base_query->first();
            $now = Carbon::now()->toDateString();
            if ($coupon) {
                $coupon_date_check = $base_query->where([
                    ['start_date', '<=', $now],
                    ['end_date', '>=', $now]
                ])->first();

                if ($coupon_date_check) {
                    if ($request->user_id) {
                        $totalCouponUses = DB::table('order_coupon')->where('coupon_id', $coupon_date_check->id)->get()->count();
                        if ($totalCouponUses < $coupon_date_check->usage_limit_of_coupon) {

                            $couponOrderByUser = DB::table('order_coupon')->where([
                                ['user_id', \auth()->user()->id],
                                ['coupon_id', $coupon->id]
                            ])->get()->count();

                            if ($couponOrderByUser >= $coupon->usage_limit_of_person) {
                                $output['response'] = false;
                                $output['msg'] = 'your coupon  limit exceeded';
                            } else {
                                $output['response'] = true;
                                $output['msg'] = 'coupon added successfully';
                                $output['result'] = $coupon_date_check->id;
                            }
                        } else {
                            $output['response'] = false;
                            $output['msg'] = 'coupon limit exceeded';
                        }
                    } else {
                        $output['response'] = false;
                        $output['msg'] = 'select user';
                    }
                } else {
                    $output['response'] = false;
                    $output['msg'] = 'coupon has been expired';
                }
            } else {
                $output['response'] = false;
                $output['msg'] = 'you have entered wrong coupon';
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function further_process($id)
    {
        $shippings = ShippingMethod::where('is_active', true)->get();
        $delivery_vendors = DB::table('delivery_vendors')->get();
        $order_status = OrderStatus::all();
        $order = Order::where('id', $id)->first();
        $orderDetails = OrderDetail::where('order_id', $id)->get();
        return view('admin.orders.order_further_process', compact(
            'shippings',
            'delivery_vendors',
            'order_status',
            'order',
            'orderDetails'
        ));
    }


    public function updateFurtherProcess(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $validator = Validator::make($request->all(), [

                "customer" => ['required', 'string'],
                'vat' => ['required', 'numeric'],
                'bill_date' => ['required'],
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

            $grossAmount = 0;
            foreach ($request->totalPrice as $price) {
                $grossAmount += floatval($price);
            }

            if ($request->discountType == 'percent') {
                $discount_amt = $grossAmount * (floatval($request->discount_per) / 100);
                $grossAmountAfterDiscount = $grossAmount - $discount_amt;
            } else {
                $discount_amt = floatval($request->discount_amt);
                $grossAmountAfterDiscount = $grossAmount - floatval($request->discount_amt);
            }


            $taxAmount = $grossAmount * ($request->vat / 100);
            $netAmount = $grossAmountAfterDiscount + $taxAmount;

            $bill_date = date('Y-m-d', strtotime($request->bill_date));
            $due_date = date('Y-m-d', strtotime($request->due_date));
            $total_coupon_discount = 0;
            for ($i = 0; $i < sizeof($request->particulars); $i++) {
                $total_coupon_discount += $request->couponDiscount[$i];
            }
            if ($request->shipping_method) {
                $netAmount += getShipingPrice($request->shipping_method);
            }

            if ($request->delivery_vendor) {
                $netAmount += $request->delivery_vendor_amount;
            }

            $order = Order::findOrFail($request->order_id);

            $order->update([
                'user_id' => $request->post('customer'),
                'delivery_vendor_id' => $request->post('delivery_vendor'),
                'billing_address_id' => $order->billing_address_id ?? '',
                'shipping_address_id' => $order->shipping_address_id ?? '',
                'shipping_method_id' => $request->shipping_method,
                'payment_type' => $order->payment_type,
                'code' => $order->code,
                'grand_total' => $grossAmount,
                'total_coupon_discount' => $total_coupon_discount,
                'net_total' => $netAmount,
                'delivery_status' => $request->order_status,
                'vat' => $request->vat,
                'discount_amount' => $discount_amt,
                'delivery_vendor_amount' => $request->delivery_vendor_amount,
                'tax_amount' => $taxAmount,
                'discountType' => $request->discountType,
                'discount_per' => $request->discount_per,
                'mode_type' => $order->mode_type,
                'coupon_code' => $order->coupon_code

            ]);

            OrderDetail::where('order_id', $request->order_id)->update([
                'delivery_status' => $request->order_status
            ]);

            DB::commit();
            $output['response'] = true;
            $output['msg'] = 'further process done';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function change_order_status(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $status = OrderStatus::all();
            $order = Order::where('id', $request->id)->first();
            $remarks = json_decode($order->remarks) ?? '';

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.change_status', compact('status', 'order', 'remarks'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function change_order_status_save(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'remarks' => ['required'],
                'order_status_id' => ['required']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            DB::beginTransaction();
            //            dd($request->remaining_amount < $request->cod_amount);

            if ($request->remaining_amount < $request->cod_amount) {

                $output['result'] = [
                    'cod_amount' => 'cod amount can not be greater than due amount'
                ];
            } else {
                $order = Order::findOrFail($request->order_id);
                $remarks = json_decode($order->remarks) ?? '';


                if ($remarks) {
                    $arryData = [
                        'status_id' => $request->order_status_id,
                        'remarks' => $request->remarks,
                        'date' => now()->toDateString()
                    ];
                    $remarks[] = $arryData;
                } else {
                    $arryData[] = [
                        'status_id' => $request->order_status_id,
                        'remarks' => $request->remarks,
                        'date' => now()->toDateString()
                    ];
                }

                if ($order->delivery_status == $request->order_status_id)
                    throw new Exception('you have not changed order status');
                $order->update([
                    'delivery_status' => $request->order_status_id,
                    'remarks' => $remarks ? json_encode($remarks) : json_encode($arryData),
                    'cod_amount' => $request->cod_amount
                ]);
                OrderDetail::where('order_id', $request->order_id)->update([
                    'delivery_status' => $request->order_status_id,
                ]);

                DB::commit();
                $output['response'] = true;
                $output['msg'] = 'order status changes successfully ';
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function check_order_status(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::findOrFail($request->order_id);

            if ($request->order_status == '1')
                throw new Exception('you can not change status to pending');
            elseif ($request->order_status == $order->delivery_status)
                $output['response'] = true;
            elseif ($request->order_status == '4' && $order->delivery_status == '2')
                throw new Exception('you can not return this product yet');
            else
                $output['response'] = true;
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function cancellation_payment_settlement(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::findOrFail($request->id);
            $payments = SalePaymentTrack::where('sale_entry_id', $request->id)->get();
            $total_paid_amount = 0;
            foreach ($payments as $payment) {
                $total_paid_amount += $payment->paid_amount;
            }

            $total_return_amount = 0;

            $expenses = Expense::where('order_id', $request->id)->get();

            foreach ($expenses as $expens) {
                $total_return_amount += $expens->total_return_amount;
            }
            $total_remaing_amount = $total_paid_amount - $total_return_amount;
            $paymnent_methods = PaymentMethod::all();
            $expenses = Expense::where('order_id', $request->id)->get();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.cancellation_settlement', compact('order', 'total_remaing_amount', 'total_return_amount', 'total_paid_amount', 'paymnent_methods', 'expenses'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function cancellation_payment_settlement_save(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'return_amount' => ['required', 'gte:0'],

            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }
            DB::beginTransaction();
            if ($request->is_settled) {
                Order::where('id', $request->order_id)->update([
                    'is_settled' => true
                ]);
            }

            $payments = SalePaymentTrack::where('sale_entry_id', $request->order_id)->get();
            $total_paid_amount = 0;
            foreach ($payments as $payment) {
                $total_paid_amount += $payment->paid_amount;
            }

            if ($request->return_amount > $request->remaining_amount)
                throw new Exception('you can not return more many than they paid');

            Expense::create([
                'order_id' => $request->order_id,
                'total_return_amount' => $request->return_amount,
                'payment_method_id' => $request->payment_method_id,
                'created_by' => \auth()->user()->id,
                'fiscal_year_id' => getFiscalYear(),
            ]);

            if ($request->return_amount == $request->remaining_amount) {
                Expense::where('order_id', $request->order_id)->update([
                    'status' => true
                ]);

                Order::where('id', $request->order_id)->update([
                    'is_settled' => true
                ]);
            }

            DB::commit();

            $output['response'] = true;
            $output['msg'] = 'payment settled successfully';
            $output['result'] = '';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function sendMail(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::where('id', $request->id)->first();
            Mail::to($order->user->email)->send(new OrderStatusToCustomer($order->delivery_status));
            $output['response'] = true;
            $output['msg'] = '';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    public function print_order(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::where('id', $request->id)->first();
            $customer_address = $order->user->addresses;
            $address = getProviceName($order->addresses->province_id) . '-' . getDistrictName($order->addresses->district_id) . '-' . $order->addresses->street;
            $payments = SalePaymentTrack::where('sale_entry_id', $request->id)->get();
            $total_paid_amount = 0;
            foreach ($payments as $payment) {
                $total_paid_amount += $payment->paid_amount;
            }
            $due_amount = $order->net_total - $total_paid_amount;
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.order_print', compact('order', 'address', 'due_amount', 'total_paid_amount'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function edit($id)
    {

        $shippings = ShippingMethod::where('is_active', true)->get();
        $delivery_vendors = DB::table('vendors')->get();
        $order_status = OrderStatus::all();
        $order = Order::where('id', $id)->first();
        $orderDetails = OrderDetail::where('order_id', $id)->get();
        $orderModes = DB::table('order_modes')->get();
        $provinces = Province::all();
        $users = User::where('is_admin', true)->get();
        $userAddress = Address::where('user_id', $order->user->id)->get();
        $address = [];
        foreach ($userAddress as $val) {
            $address[] = [
                'id' => $val->id,
                'address_name' => getProviceName($val->province_id) . '-' . getDistrictName($val->district_id) . '-' . $val->street
            ];
        }

        return view('admin.orders.edit', compact(
            'shippings',
            'delivery_vendors',
            'order_status',
            'order',
            'orderDetails',
            'orderModes',
            'provinces',
            'users',
            'address',
        ));
    }

    public function update(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [

                "customer" => ['required', 'string'],
                'vat' => ['required', 'numeric'],
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

            $grossAmount = 0;
            foreach ($request->totalPrice as $price) {
                $grossAmount += floatval($price);
            }

            if ($request->discountType == 'percent') {
                $discount_amt = $grossAmount * (floatval($request->discount_per) / 100);
                $grossAmountAfterDiscount = $grossAmount - $discount_amt;
            } else {
                $discount_amt = floatval($request->discount_amt);
                $grossAmountAfterDiscount = $grossAmount - floatval($request->discount_amt);
            }


            $taxAmount = $grossAmount * ($request->vat / 100);
            $netAmount = $grossAmountAfterDiscount + $taxAmount;

            $bill_date = date('Y-m-d', strtotime($request->bill_date));
            $due_date = date('Y-m-d', strtotime($request->due_date));
            $total_coupon_discount = 0;
            for ($i = 0; $i < sizeof($request->particulars); $i++) {
                $total_coupon_discount += $request->couponDiscount[$i];
            }
            if ($request->shipping_method) {
                $netAmount += getShipingPrice($request->shipping_method);
            }

            $delivery_charge = $request->delivery_vendor_amount_to_customer;
            if ($request->delivery_vendor) {
                $netAmount += $delivery_charge;
            }


            $order = Order::findOrFail($request->order_id);

            $order->update([
                'user_id' => $request->post('customer'),
                'delivery_vendor_id' => $request->post('delivery_vendor'),
                'shipping_address_id' => $request->shipping_address_id,
                'shipping_method_id' => $request->shipping_method,
                'payment_type' => $order->payment_type,
                'code' => $order->code,
                'grand_total' => $grossAmount,
                'total_coupon_discount' => $total_coupon_discount,
                'net_total' => $netAmount,
                'delivery_status' => $request->order_status,
                'vat' => $request->vat,
                'discount_amount' => $discount_amt,
                'delivery_vendor_amount' => $delivery_charge,
                'tax_amount' => $taxAmount,
                'discountType' => $request->discountType,
                'discount_per' => $request->discount_per,
                'mode_type' => $order->mode_type,
                'coupon_code' => $order->coupon_code,
                'order_by' => $request->order_by,
                'delivery_amount_by_vendor' => $request->delivery_vendor_amount,
                'logistic_order_code' => $request->logistic_order_code,
                'logistic_token' => $request->logistic_vendor_given_code,
                'date' => $request->estimated_date,
                'customer_remarks' => $request->customer_remarks
            ]);

            $product_profit = 0;
            for ($i = 0; $i < sizeof($request->post('particulars')); $i++) {


                $productId = $request->particulars[$i];
                $attributeId = ($request->variants[$i]);

                $product = ProductMaster::where([
                    ['product_id', $productId],
                    ['attribute_id', $attributeId],
                    ['is_active', true]
                ])->latest()->first();
                $product = $product->profit_margin ?? 0;
                // dd($product);
                $product_profit += ($product * $request->post('quantity')[$i]);


                if ($request->orderDetails[$i]) {
                    $orderDetails = OrderDetail::findOrFail($request->orderDetails[$i]);

                    $orderDetails->update([
                        'product_id' => $request->post('particulars')[$i],
                        'delivery_status' => $request->post('order_status'),
                        'price' => $request->post('unitPrice')[$i],
                        'quantity' => $request->post('quantity')[$i],
                        'product_coupon_discount' => $request->post('couponDiscount')[$i],
                    ]);
                } else {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'code' => $order->code,
                        'product_id' => $request->post('particulars')[$i],
                        'delivery_status' => $request->post('order_status'),
                        'price' => $request->post('unitPrice')[$i],
                        'quantity' => $request->post('quantity')[$i],
                        'product_coupon_discount' => $request->post('couponDiscount')[$i],
                    ]);
                }
            }

            $net_profit = $product_profit + $delivery_charge - $request->delivery_vendor_amount;


            DB::table('sales_profit_loss')->where('order_id', $request->order_id)->update([
                'order_id' => $request->order_id,
                'net_amount' => $net_profit
            ]);

            DB::commit();
            $output['response'] = true;
            $output['msg'] = 'further process done';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    function countTotalStatusRecords(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $status = $request->status_name;

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.countTotalStatusRecords', compact('status'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    function countFilteredRecords(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $fiscal_year = getFiscalYear();
            $pendings = 0;
            $processing = 0;
            $delivered = 0;
            $returned = 0;
            $cancelled = 0;
            $status = $request->status;


            $pendings =
                Order::where([
                    ['delivery_status', '1']
                ])->count();

            $processing = Order::where([
                ['delivery_status', '2']
            ])->count();

            $delivered = Order::where([
                ['delivery_status', '3']
            ])->count();

            $returned = Order::where([
                ['delivery_status', '4']
            ])->count();

            $cancelled = Order::where([
                ['delivery_status', '=', '8']
            ])->count();

            if ($request->fiscal_year) {
                $pendings = Order::where([
                    ['fiscal_year_id', $request->fiscal_year],
                    ['delivery_status', '1']
                ])->count();

                $processing = Order::where([
                    ['fiscal_year_id', $request->fiscal_year],
                    ['delivery_status', '2']
                ])->count();

                $delivered = Order::where([
                    ['fiscal_year_id', $request->fiscal_year],
                    ['delivery_status', '3']
                ])->count();

                $returned = Order::where([
                    ['fiscal_year_id', $request->fiscal_year],
                    ['delivery_status', '4']
                ])->count();

                $cancelled = Order::where([
                    ['fiscal_year_id', $request->fiscal_year],
                    ['delivery_status', '=', '8']
                ])->count();
            }

            if ($request->start_date && $request->end_date) {
                $pendings =
                    Order::where([
                        ['date', '>=', $request->start_date],
                        ['date', '<=', $request->end_date],
                        ['delivery_status', '1']
                    ])->count();

                $processing = Order::where([
                    ['date', '>=', $request->start_date],
                    ['date', '<=', $request->end_date],
                    ['delivery_status', '2']
                ])->count();

                $delivered = Order::where([
                    ['date', '>=', $request->start_date],
                    ['date', '<=', $request->end_date],
                    ['delivery_status', '3']
                ])->count();

                $returned = Order::where([
                    ['date', '>=', $request->start_date],
                    ['date', '<=', $request->end_date],
                    ['delivery_status', '4']
                ])->count();

                $cancelled = Order::where([
                    ['date', '>=', $request->start_date],
                    ['date', '<=', $request->end_date],
                    ['delivery_status', '=', '8']
                ])->count();
            }

            if ($request->year && $request->month) {

                $year = $request->year;
                $month = $request->month;

                $date = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $start_date = "$year-$month-01";
                $end_date = "$year-$month-$date";

                $pendings =
                    Order::where([
                        ['date', '>=', $start_date],
                        ['date', '<=', $end_date],
                        ['delivery_status', '1']
                    ])->count();

                $processing = Order::where([
                    ['date', '>=', $start_date],
                    ['date', '<=', $end_date],
                    ['delivery_status', '2']
                ])->count();

                $delivered = Order::where([
                    ['date', '>=', $start_date],
                    ['date', '<=', $end_date],
                    ['delivery_status', '3']
                ])->count();

                $returned = Order::where([
                    ['date', '>=', $start_date],
                    ['date', '<=', $end_date],
                    ['delivery_status', '4']
                ])->count();

                $cancelled = Order::where([
                    ['date', '>=', $start_date],
                    ['date', '<=', $end_date],
                    ['delivery_status', '=', '8']
                ])->count();
            }


            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.countFilteredRecords', compact('pendings', 'cancelled', 'returned', 'delivered', 'processing', 'status'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    public function viewOrder(Request $request, $code)
    {

        $decoded_code = base64_decode($code);
        $order = Order::where('code', $decoded_code)->first();

        return view('admin.orders.view', compact('order'));
    }

    public function getCustomerProfile(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {
            $order = Order::findOrFail($request->order_id);

            $user = User::findOrFail($order->user_id);
            $payments = SalePaymentTrack::where('sale_entry_id', $request->order_id)->get();

            $total_paid_amount = 0;
            foreach ($payments as $payment) {

                $total_paid_amount += $payment->paid_amount;
            }
            $remaining_amount = $order->net_total - $total_paid_amount;

            $addresses = Address::where('user_id', $user->id)->get();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.view_customer', compact('user', 'addresses', 'order', 'total_paid_amount', 'remaining_amount'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function view_logistic_vendor_order_log(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $vendor = DB::table('vendors')->where('id', $request->vendor_id)->first();
            if ($vendor->api_base_url) {
                $user = User::where('id', $request->user_id)->first();
                $orderLogs = getLogistictVendorOrderLog($vendor->api_base_url, $request->logistic_order_code, $request->token);
                $output['response'] = true;
                $output['msg'] = '';
                $output['result'] = view('admin.orders.view_logistic_customer_log', compact('orderLogs', 'user'))->render();
            } else {
                throw new Exception('this vendor has no api base url');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    public function pay_to_logistic_vendor(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::where('id', $request->order_id)->first();
            $logistic_payments = LogistiPaymentTrack::where('order_id', $order->id)->get();

            $logistic_total_paid_amount = 0;
            foreach ($logistic_payments as $payment) {
                $logistic_total_paid_amount += $payment->paid_amount;
            }
            $logistic_remain_paid_amount = intval($order->delivery_amount_by_vendor) - intval($logistic_total_paid_amount);
            $payment_methods = PaymentMethod::where('status', true)->get();

            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.orders.logistic_payment_view', compact('order', 'logistic_total_paid_amount', 'logistic_remain_paid_amount', 'payment_methods', 'logistic_payments'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function getCustomerPayment(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::findOrFail($request->order_id);
            $payments = SalePaymentTrack::where('sale_entry_id', $request->order_id)->get();

            $total_paid_amount = 0;
            foreach ($payments as $payment) {
                $total_paid_amount += $payment->paid_amount;
            }
            $remaining_amount = $order->net_total - $total_paid_amount;

            $output['response'] = true;
            $output['msg'] = '';
            $output['total_amount'] = $order->net_total;
            $output['total_paid_amount'] = $total_paid_amount;
            $output['remaining_amount'] = $remaining_amount;
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function order_show($code)
    {

        $decode_code = base64_decode($code);
        $order = Order::where('code', $code)->first();

        $address = getProviceName($order->addresses->province_id) . '-' . getDistrictName($order->addresses->district_id) . '-' . $order->addresses->street;
        $payments = SalePaymentTrack::where('sale_entry_id', $order->id)->get();
        $total_paid_amount = 0;
        $shippingPrice = getShipingPrice($order->shipping_method_id);
        foreach ($payments as $payment) {
            $total_paid_amount += $payment->paid_amount;
        }
        $due_amount = $order->net_total + $shippingPrice - $total_paid_amount;
        $paymentMethods = PaymentMethod::where('status', true)->get();
        return view('admin.orders.complete_order', compact('order', 'address', 'due_amount', 'total_paid_amount', 'paymentMethods'));
    }

    public function complete_order_from_url(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $validator = Validator::make($request->all(), [
                'mpin' => ['required', 'digits:6'],
                'received_amount' => ['required', 'gte:0']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new \Exception("Form Validation Error");
            }

            if (($request->received_amount) > ($request->due_amount)) {
                $output['result'] = [
                    'received_amount' => 'Received Amount Cannot be  more  than Due Amount'
                ];
                throw new \Exception("Form Validation Error");
            }

            $user = User::where('mpin', $request->mpin)->first();

            if (!($user)) {
                $output['result'] = [
                    'mpin' => 'mpin does not exists'
                ];
                throw new \Exception("Form Validation Error");
            }
            SalePaymentTrack::create([
                'sale_entry_id' => $request->order_id,
                'payment_method_id' => $request->payment_method_id,
                'paid_amount' => $request->received_amount,
                'created_by' => $user->id,
                'fiscal_year_id' => getFiscalYear()
            ]);

            Order::where('id', $request->order_id)->update([
                'delivery_status' => 3
            ]);

            OrderDetail::where('order_id', $request->order_id)->update([
                'delivery_status' => 3
            ]);

            $output['response'] = true;
            $output['msg'] = 'ordered completed successfully';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }
}
