<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductMaster;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PurchaseReturnController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('purchase_return');
        $fiscalYear = DB::table('fiscal_year_master')->get();
        return view('admin.purchase_return.index',compact('fiscalYear'));
    }

    public function getPurchaseReturn(Request $request): JsonResponse
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
        $query = DB::table('purchase_return_details')->orderBy($columnName, $columnSortOrder)
            ->leftJoin('purchase_return_master','purchase_return_details.purchase_return_id','purchase_return_master.id')
            ->leftJoin('products','products.id','purchase_return_details.product_id')
            ->select('purchase_return_master.*','products.name')
            ->orWhere('products.name', 'like', '%' . $searchValue . '%')
             ->groupBy('purchase_return_details.product_id');

        $totalRecords = $query->get()->count();
        $totalRecordswithFilter =$query->get()->count();;
        /*Fetch records*/

        if ($request->fiscal_year){
          $records =   $query->where('purchase_return_master.fiscal_year_id',$request->fiscal_year)->take($rowperpage)
                ->get();
        }
        else if ($request->length == -1) {
            $records =
                $query->where('purchase_return_master.fiscal_year_id',$fiscal_year)
                    ->take($rowperpage)
                    ->get();
        } else {
            $records = $query->where('purchase_return_master.fiscal_year_id',$fiscal_year)
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }



        $data_arr = array();

        $i = $start + 1;
        foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $i,
                'fiscal_year_id'=>getFiscalYearName($record->fiscal_year_id),
                "product_id" => $record->name,
                "total_price" => $record->total_price,
                "action" => "<a class='view_sale btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='#'  onclick='viewPurchaseReturn($record->id)'>View</a> &nbsp;&nbsp;<a class='btn btn-warning btn-sm btn-rounded waves-effect waves-light'  href='javascript:'>Delete</a>"

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

            $product = Product::where('id',$request->product_id)->first();
            $details = ProductMaster::where([
                ['product_id',$product->id],
                ['is_active',true]
            ])->groupBy('attribute_id')->get();
            $grand_total = 0;

            foreach ($details  as $val){
                $grand_total += $val->quantity * $val->price;
            }
            $purchaseReturn = PurchaseReturn::create([
                'total_price' => $grand_total,
                'fiscal_year_id'=>getFiscalYear(),
                'created_by'=>auth()->user()->id
            ]);

            foreach ($details as $detail){
                PurchaseReturnDetail::create([
                    'purchase_return_id'=>$purchaseReturn->id,
                    'product_id'=>$product->id,
                    'product_attribute_id'=>$detail->attribute_id,
                    'cost_price'=>$detail->cost_price,
                    'selling_price'=>$detail->price,
                    'qty'=>$detail->quantity,
                    'total_price'=>  ($detail->quantity * $detail->price)
                ]);
            }
            $output['response'] = true;
            $output['msg'] = 'Purchase return  Successfully';

            DB::commit();

        } catch (Exception $exception) {
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

            $details = PurchaseReturnDetail::where('purchase_return_id',$request->id)->get();
            $output['response'] = true;
            $output['result']= view('admin.purchase_return.view',compact('details'))->render();
        }catch (\Exception $exception){
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }


    public function getProductDetailsBySku(Request $request){
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $product = Product::where('sku',$request->sku)->first();
            $details = ProductMaster::where([
                ['product_id',$product->id],
                ['is_active',true]
            ])->groupBy('attribute_id')->get();

            $view = view('admin.purchase_return.product_details',compact('product','details'))->render();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $view;

        } catch (\Exception $exception) {
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }
}
