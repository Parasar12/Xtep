<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\FlashSale;
use App\Models\FlashSaleProducts;
use App\Models\Product;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Exception;
use Illuminate\Support\Facades\Response;

class FlashSalesController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        $products = Product::where('is_active', 1)->get();
        return view('admin.flash-sales.index', compact('products'));
    }

    public function store(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        try {

            $products = $request->products;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $price = $request->price;
            $quantity = $request->quantity;

            $requestData = $request->all();
            $length = count($requestData['products']);

            if ($length == 0) {
                throw new Exception("Invalid Entry.");
            }

            for ($i = 0; $i < $length; $i++) {

                $validator = Validator::make($requestData, [
                    "name" => "required|max:250|unique:flash_sales,name",
                    "start_date" => "required|date",
                    "end_date" => "required|date",

                    "products.*" => "required|string|distinct",

                ]);
            }

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            }

            for ($i = 0; $i < $length; $i++) {

                if (strtotime($requestData['start_date'][$i]) > strtotime($requestData['end_date'][$i])) {
                    throw new Exception("The end_date.$i field must be after or equal to start_date.$i");
                }
            }
            DB::beginTransaction();
            $sale = new FlashSale();
            $sale->name = $request->name;
            $sale->is_active = 1;
            $sale->start_date = $request->start_date;
            $sale->end_date = $request->end_date;
            $sale->created_by = auth()->user()->id;
            $sale->save();

            if ($sale->id) {
                $saleId = $sale->id;
                for ($i = 0; $i < $length; $i++) {
                    $saleProduct = new FlashSaleProducts();
                    $saleProduct->flash_sale_id = $saleId;
                    $saleProduct->product_id = $requestData['products'][$i];
                    $saleProduct->price = $requestData['price'][$i];
                    $saleProduct->quantity = $requestData['quantity'][$i];
                    $saleProduct->save();
                }
            }

            DB::commit();

            $output = array(
                'response' => True,
                'msg' => "Flash Sales Created Successfully",
                'result' => "",
            );

        } catch (Exception $e) {
            DB::rollBack();

            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function edit($id)
    {
        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        $sale = FlashSale::where('id', $id)->first();
        if ($sale) {
            $products = Product::where('is_active', 1)->get();
            $flashProducts = FlashSaleProducts::where('flash_sale_id', $id)->get();
            return view('admin.flash-sales.edit', compact('products', 'sale', 'flashProducts'));
        } else {
            return redirect('flash-sales')->with('status', 'Flash Sale Not Found');
        }
    }

    public function update(Request $request, $id)
    {
        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        try {

            $products = $request->products;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $price = $request->price;
            $quantity = $request->quantity;

            $length = count($products);
            $requestData = $request->all();

            unset($requestData['products'][$length]);
            unset($requestData['price'][$length]);
            unset($requestData['quantity'][$length]);

            $length = count($requestData['products']);

            if ($length == 0) {
                throw new Exception("Invalid Entry.");
            }

            for ($i = 0; $i < $length; $i++) {

                $validator = Validator::make($requestData, [
                    "name" => "required|max:250|unique:flash_sales,name," . $id,
                    "start_date" => "required|date",
                    "end_date" => "required|date",

                    "products.*" => "required|numeric|distinct",
                    "price.*" => "required|numeric",
                    "quantity.*" => "required|numeric",
                ]);
            }

            if ($validator->fails()) {
                $output = array(
                    'result' => $validator->errors()->toArray(),
                );

                throw new Exception("Form Validation Error");
            }

            for ($i = 0; $i < $length; $i++) {

                if (strtotime($requestData['start_date'][$i]) > strtotime($requestData['end_date'][$i])) {
                    throw new Exception("The end_date.$i field must be after or equal to start_date.$i");
                }
            }

            DB::beginTransaction();

            $sale = FlashSale::find($id);
            $sale->name = $request->name;
            $sale->start_date = $request->start_date;
            $sale->end_date = $request->end_date;
            $sale->is_active = 1;
            $sale->save();

            if ($sale->id) {
                FlashSaleProducts::where('flash_sale_id', $id)->delete();

                $saleId = $sale->id;
                for ($i = 0; $i < $length; $i++) {
                    $saleProduct = new FlashSaleProducts();
                    $saleProduct->flash_sale_id = $saleId;
                    $saleProduct->product_id = $products[$i];
                    $saleProduct->price = $price[$i];
                    $saleProduct->quantity = $quantity[$i];
                    $saleProduct->save();
                }
            }

            DB::commit();

            $output = array(
                'response' => True,
                'msg' => "Flash Sales Updated Successfully",
                'result' => "",
            );
        } catch (Exception $e) {
            DB::rollBack();

            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function getFlashSales(Request $request)
    {
        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        $length = $request->post('length');
        $start = $request->post('start');
        $columns = $request->post('columns');
        $order = $request->post('order');

        $orderBySql = "ORDER BY " . $columns[$order[0]['column']]['data'] . " " . $order[0]['dir'];
        $whereAllSql = "WHERE 1 ";
        foreach ($columns as $key => $value) {
            if (!empty($request->post('search')['value']) && 'action' != $value['data']) {
                $init = (0 == $key) ? 'AND ' : 'OR ';

                $sqlString = $init . $value['data'] . " LIKE '%" . $request->post('search')['value'] . "%' ";
                $whereAllSql .= $sqlString;
            }
        }

        $basicQuery = FlashSale::select(DB::raw('CASE WHEN is_active = 1 THEN "Active" ELSE "Not-Active" END AS status, id, name, start_date, end_date'))
            ->toSql();

        if ($length == -1) {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql";
        } else {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql LIMIT $start, $length";
        }

        $queryResult = DB::select($dataQuery);

        $result = array();
        foreach ($queryResult as $value) {
            $result[] = array(
                "name" => $value->name,
                "start_date" => $value->start_date,
                "end_date" => $value->end_date,
                "status" => $value->status,
                "action" => "<a class='edit_flash_sales btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('flash-sales.edit', $value->id) . "'>Edit</a>&nbsp;&nbsp;<a class='delete_flash_sales btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $value->id . "' href='javascript:'>Delete</a>"
            );
        }

        $recordsFilteredQuery = "SELECT count(id) AS records_filtered FROM ($basicQuery) a $whereAllSql";
        $recordsFiltered = DB::select($recordsFilteredQuery);
        $recordsFiltered = $recordsFiltered[0]->records_filtered;

        $response = array(
            'draw' => intval($request->post('draw')) + 1,
            'recordsTotal' => $recordsFiltered,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        );

        print(json_encode($response));
        exit();
    }

    function deleteFlashSales(Request $request)
    {

        $this->_hasMenuAccess('HomePageSetup-FlashSales');

        try {
            DB::beginTransaction();
            FlashSale::where('id', $request->saleId)->delete();
            FlashSaleProducts::where('flash_sale_id', $request->saleId)->delete();

            DB::commit();

            $output = array(
                'response' => True,
                'msg' => " Flash Sales Delete Successfully",
                'result' => "",
            );

        } catch (Exception $e) {
            DB::rollBack();

            $output['response'] = FALSE;
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }
}
