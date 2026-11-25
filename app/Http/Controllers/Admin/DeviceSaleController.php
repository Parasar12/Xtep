<?php

namespace App\Http\Controllers\Admin;


use App\Models\DeviceSale;
use Illuminate\Http\Request;

class DeviceSaleController extends Controller
{
    //
    public function index(){
        $this->_hasMenuAccess('DeviceManagement-Sales');

        return view('admin.device_management.sales.index');
    }

    public function getSales(Request $request)
    {
        $this->_hasMenuAccess('DeviceManagement-Sales');

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
      $totalRecords = DeviceSale::select('count(*) as allcount')->count();
     
      $totalRecordswithFilter = DeviceSale::select('count(*) as allcount')->where('brand', 'like', '%' . $searchValue . '%')->count();

     
      /*Fetch records*/
      if ($request->length == -1) {
        $records = DeviceSale::orderBy($columnName, $columnSortOrder)
          ->join('users','device_sales_master.user_id','users.id')
          ->where([
            ['device_sales_master.brand', 'like', '%' . $searchValue . '%'],
          ])
          ->select('device_sales_master.*','users.name')
          ->take($rowperpage)
          ->get();
      } else {
        $records = DeviceSale::orderBy($columnName, $columnSortOrder)
        ->join('users','device_sales_master.user_id','users.id')
          ->where([
            ['device_sales_master.brand', 'like', '%' . $searchValue . '%'],
          ])
          ->select('device_sales_master.id','device_sales_master.brand','device_sales_master.model','device_sales_master.ram','device_sales_master.storage','users.name')
          ->skip($start)
          ->take($rowperpage)
          ->get();
      }

      $data_arr = array();
  
      $i = 1;
      foreach ($records as $record) {
        $customer_name = $record->name;
        $brand = $record->brand;
        $model = $record->model;
        $ram = $record->ram;
        $storage = $record->storage;
        $id = $record->id;
  
        $data_arr[] = array(
          "id" => $i,
          "customer_name" => $customer_name,
          "brand" => $brand,
          "model" => $model,
          "ram" => $ram,
          "storage" => $storage,
          'action' => "<a class='view_order btn btn-primary btn-sm btn-rounded waves-effect waves-light' style='color:white' onclick='showOrderDetailModal($id)'>View</a>"
        );
        $i++;
      }
  
      $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordswithFilter,
        "aaData" => $data_arr,
      );
  
      print(json_encode($response));
      exit();
    }

    public function saleDetail($id){
        $sale = DeviceSale::where('id',$id)->first();
        $device_conditions = json_decode($sale->device_condition);
        $device_accessories = json_decode($sale->device_accessories);
        $device_problems = json_decode($sale->device_problems);
       
        return view('admin.device_management.sales.details',compact('device_conditions','device_accessories','device_problems'));
    }
}
