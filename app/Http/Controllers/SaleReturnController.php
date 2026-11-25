<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductMaster;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SaleReturnController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('sales_return');
        $fiscalYear = DB::table('fiscal_year_master')->get();
        return view('admin.sales_return.index',compact('fiscalYear'));
    }

    public function getSalesReturn(Request $request): JsonResponse
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

        $fiscal_year = getFiscalYear();

        $query = DB::table('sales_return')->orderBy($columnName, $columnSortOrder)
            ->join('users','users.id','sales_return.user_id')
            ->select('sales_return.*','users.name')
            ->orWhere('users.name', 'like', '%' . $searchValue . '%');

        $totalRecords = $query->get()->count();
        $totalRecordswithFilter =$query->get()->count();;
        /*Fetch records*/
        if($request->fiscal_year){
            $records = $query->where('sales_return.fiscal_year_id',$request->fiscal_year) ->take($rowperpage)
                ->get();
        }
        else if ($request->length == -1) {
            $records =
                $query->where('sales_return.fiscal_year_id',$fiscal_year)
                    ->take($rowperpage)
                    ->get();
        } else {
            $records = $query->where('sales_return.fiscal_year_id',$fiscal_year)
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }



        $data_arr = array();

        $i = $start + 1;
        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $i,
                "fiscal_year_id" => getFiscalYearName($record->fiscal_year_id),
                "user_id" => $record->name,
                'total_coupon_discount' => $record->total_coupon_discount,
                "grand_total" => $record->grand_total,
                "discount_amount" => $record->discount_amount,
                "tax_amount" => $record->tax_amount,
                "net_total" => $record->net_total,
                "action" => "<a class='view_sale btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='#'  onclick='viewSalesReturn($record->id)'>View</a> &nbsp;&nbsp;<a class='btn btn-warning btn-sm btn-rounded waves-effect waves-light'  href='javascript:'>Delete</a>"

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

    public function store(Request $request): JsonResponse
    {
        $output = [
            'response' => false,
            'msg' => "Error while Processing Please Try again.",
            "result" => ""
        ];
        try {

            DB::beginTransaction();

            $order = Order::where('id',$request->order_id)->first();
            if(SaleReturn::where('code',$order->code)->exists())
                throw new \Exception("Sales already returned");

            $sale = new SaleReturn();
            $sale->code = $order->code;
            $sale->user_id = $order->user_id;
            $sale->delivery_vendor_id = $order->delivery_vendor_id ;
            $sale->vat = $order->vat;
            $sale->grand_total = $order->grand_total;
            $sale->discount_amount =  $order->discount_amount ;
            $sale->shipping_method_id =  $order->shipping_method_id;
            $sale->tax_amount =  $order->tax_amount;
            $sale->net_total =  $order->net_total;
            $sale->delivery_status =  $order->delivery_status;
            $sale->created_by = \auth()->user()->id;
            $sale->mode_type = $order->mode_type;
            $sale->discount_per = $order->discount_per;
            $sale->discountType =  $order->discountType;
            $sale->delivery_vendor_amount = $order->delivery_vendor_amount;
            $sale->total_coupon_discount =  $order->total_coupon_discount;
            $sale->fiscal_year_id = getFiscalYear();
            $sale->save();


            $details = OrderDetail::where([
                ['order_id',$request->order_id],
            ])->get();

            foreach ($details as $detail){

                SaleReturnDetail::create([
                    'sales_return_id' => $sale->id,
                    'product_id' => $detail->product_id,
                    'code' => $detail->code,
                    'quantity'=>$detail->quantity,
                    'price' => $detail->price,
                    'product_coupon_discount'=>$detail->product_coupon_discount
//                    'total_amount' => $request->totalPrice[$i],
                ]);
            }

            $output['response'] = true;
            $output['msg'] = 'Sales return  Successfully';

            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
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
            $order  = SaleReturn::where('id',$request->sales_return_id)->first();
            $details   = SaleReturnDetail::where('sales_return_id',$request->sales_return_id)->get();

            $output['response'] = true;
            $output['result']= view('admin.sales_return.view',compact('order','details'))->render();
        }catch (\Exception $exception){
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function searchOrderCode(Request $request): JsonResponse
    {

        $orders = Order::select('id', 'code')
            ->where([
                ['code', 'like', '%' . $request->q . '%'],
            ])
            ->limit(20)
            ->get();
        $result = array();
        if ($orders->count()) {
            foreach ($orders as $order) {
                $row ['id'] = $order->id;
                $row['text'] = $order->code ;

                $result[] = $row;
            }
        }

//        else{
//            $row ['id'] = $request->q;
//            $row['text'] = $request->q;
//
//            $result[] = $row;
//        }

        $output = array(
            'result' => array('results' => $result),
            'response' => true,
            'msg' => '',
        );

        return \Response::json($output);
    }


    public function getOrderDetailsByCode(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $order = Order::where('id',$request->sku)->first();

            $details = OrderDetail::where([
                ['order_id',$request->sku],
            ])->get();

            $view = view('admin.sales_return.order_details',compact('order','details'))->render();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $view;

        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

}
