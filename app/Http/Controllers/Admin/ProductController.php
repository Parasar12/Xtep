<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\ProductGallary;
use App\Models\ProductAttribute;
use App\Models\ProductMaster;
use App\Models\OrderDetail;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Validator;
use Exception;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{

    public function index()
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $categories = Category::where('parent_id', 0)->where('is_active', 1)->with('children')->get();
        $brands = Brand::where('is_active', 1)->get();
        $attributes = Attribute::all();
        $fiscalYear = DB::table('fiscal_year_master')->get();
        $products = Product::where('is_active', 1)->get();
        return view('admin.products.index', compact('categories', 'fiscalYear', 'brands', 'attributes', 'products'));
    }

    public function getProducts(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $length = $request->post('length');
        $start = $request->post('start');
        $columns = $request->post('columns');
        $order = $request->post('order');

        $orderBySql = "ORDER BY " . $columns[$order[0]['column']]['data'] . " " . $order[0]['dir'];
        $whereAllSql = "WHERE 1 ";
        foreach ($columns as $key => $value) {
            if (!empty($request->post('search')['value']) && 'action' != $value['data'] && 'checkbox' != $value['data']) {
                $init = (0 == $key) ? 'AND ' : 'OR ';

                $sqlString = $init . $value['data'] . " LIKE '%" . $request->post('search')['value'] . "%' ";
                $whereAllSql .= $sqlString;
            }
        }
        $fiscal_year = getFiscalYear();
        if ($request->fiscal_year) {
            $basicQuery = "SELECT p.*, c.name  as category_name,
                CASE
                When p.is_active = 1 Then 'Active'
                ELSE 'Not-Active'
                END As status
                From products p
                LEFT Join categories  c on c.id = p.category_id
                where p.is_active = 1
                 AND p.fiscal_year_id =$request->fiscal_year";
        } else {
            $basicQuery = "SELECT p.*, c.name  as category_name,
                CASE
                When p.is_active = 1 Then 'Active'
                ELSE 'Not-Active'
                END As status
                From products p
                LEFT Join categories  c on c.id = p.category_id

                where p.is_active = 1
                  AND p.fiscal_year_id = $fiscal_year
                ";
        }



        if ($length == -1) {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql";
        } else {
            $dataQuery = "SELECT * FROM ($basicQuery) a $whereAllSql $orderBySql LIMIT $start, $length";
        }


        $queryResult = DB::select($dataQuery);
        $result = array();
        foreach ($queryResult as $value) {
            $result[] = array(
                'fiscal_year_id' => getFiscalYearName($value->fiscal_year_id),
                "name" => $value->name,
                "vendor_id" => getVendorName($value->vendor_id),
                "category_name" => $value->category_name,
                "created_by" => getUserName($value->created_by),
                "status" => $value->status,
                'action' => "<a class='edit_product btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('products.edit', $value->id) . "'>Edit</a>&nbsp;&nbsp;<a class='btn btn-warning btn-sm btn-rounded waves-effect waves-light' onclick='viewProducts( $value->id)' href='javascript:'>View</a> <a class='delete_product btn btn-danger btn-sm btn-rounded waves-effect waves-light' data-id='" . $value->id . "' href='javascript:'>Delete</a>",
                'checkbox' => "<input type='checkbox'  id='multiple_delete-$value->id' class='multiple_delete' name='multiple_delete' onchange='arrayProducts($value->id,this)' value='" . $value->id . "'>",

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

    /************* Product Stock Report Start *************/

    public function productInStock()
    {
        $this->_hasMenuAccess('Reports-ProductStockReport');

        $products = Product::where('is_active', true)
            ->select('id', 'name')
            ->get();

        return view('admin.products.stock', compact('products'));
    }

    public function getProductStockCount(Request $request)
    {
        $this->_hasMenuAccess('Reports-ProductStockReport');

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
        $totalRecords = Product::where('is_active', true)->select('count(*) as allcount')->count();
        $totalRecordswithFilter = Product::where('is_active', true)->select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%' . $searchValue . '%')
                ->where('is_active', true)
                ->select('id', 'name')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%' . $searchValue . '%')
                ->where('is_active', true)
                ->select('id', 'name')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;

        foreach ($records as $record) {
            $total = 0;
            $ordered = 0;
            $productMasters = ProductMaster::where([
                ['product_id', $record->id],
                ['is_active', true]
            ])->get();

            foreach ($productMasters as $master) {

                if ($record->id == $master->product_id) {
                    $total = intVal($total) + intVal($master->quantity);
                }
            }

            $name = $record->name;

            foreach ($record->orderDetails as $detail) {
                if ($detail->delivery_status == '3') {
                    if ($detail->product_id == $record->id) {
                        $ordered = intVal($ordered) + intVal($detail->quantity);
                    }
                }
            }

            $data_arr[] = array(
                "id" => $i,
                "name" => $record->name,
                "total" => $total,
                "order" => $ordered,
                "remaining" => $total - $ordered,
                "action" => "<button onclick='viewDetails($record->id)' class='btn btn-info btn-sm'>View</button>"
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

    /************** Product Stock Report End ***************/


    function getProductSellingPriceAndQuantity($id)
    {
        $product = ProductMaster::where('product_id', $id)->where('is_active', 1)->first();

        $data = [
            'price' => $product->price,
            'quantity' => $product->quantity,
        ];

        return $data;
    }

    public function bulkImport()
    {
        $this->_hasMenuAccess('ProductSetup-BulkImportExport');

        return view('admin.products.bulk_import');
    }

    public function bulkImportStore(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-BulkImportExport');

        $fileValidator = Validator::make($request->all(), [
            'products_file' => 'required|mimes:xls,xlsx'
        ]);

        if ($fileValidator->fails()) {
            return response()->json(['status' => 0, 'error' => $fileValidator->errors()->toArray()]);
        }

        try {
            if ($request->file('products_file')) {
                Excel::import(new ProductsImport, $request->file('products_file'));

                return response()->json(['status' => 1, 'msg' => 'Bulk Products Imported Successfully.']);
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            return response()->json(['status' => 0, 'error' => $e->failures()]);
        }
    }

    function checkExistingProduct($id)
    {
        return Product::where('id', $id)->first();
    }

    public function gallaryUpdate(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Gallery');

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {

            $files = $request->file('image');

            $today = today();
            if (isset($files)) {
                foreach ($files as $file) {
                    $extension = $file->extension();
                    $imageName = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();

                    $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $imageName);

                    $gallary = new ProductGallary();
                    $gallary->product_id = $request->id;
                    $gallary->gallary_images = $imageName;

                    $gallary->save();
                }
                return response()->json(['status' => 1, 'msg' => "Product Gallary Updated Successfully."]);
            } else {
                return response()->json(['status' => 0, 'error' => ["image" => ["No changes to update."]]]);
            }
        }
    }

    public function gallaryEdit($id)
    {
        $this->_hasMenuAccess('ProductSetup-Gallery');

        $product = Product::where('id', $id)->first();
        $gallary = ProductGallary::where('product_id', $id)->get();
        $products = Product::all();

        return view('admin.gallary.edit', compact('product', 'gallary', 'id', 'products'));
    }

    public function gallary()
    {
        $this->_hasMenuAccess('ProductSetup-Gallery');

        $products = Product::all();
        return view('admin.gallary.index', compact('products'));
    }

    public function getProductGallary(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Gallery');

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
        $totalRecords = Product::where('is_active', true)->select('count(*) as allcount')->count();
        $totalRecordswithFilter = Product::where([
            ['is_active', true],
            ['name', 'like', '%' . $searchValue . '%'],
        ])->select('count(*) as allcount')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('is_active', true)
                ->select('id', 'name')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('is_active', true)
                ->select('id', 'name')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;


            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                'action' => "<a class='edit_brand btn btn-primary btn-sm btn-rounded waves-effect waves-light' href='" . route('product.gallary.edit', $id) . "'>Add/Edit Gallary</a>"
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        print(json_encode($response));
        exit();
    }

    public function gallaryStore(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Gallery');

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
            'image.*' => 'image|mimes:jpeg,png,jpg,JPG,JPEG,PNG|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'error' => $validator->errors()->toArray()]);
        } else {

            $files = $request->file('image');

            $today = today();
            if (isset($files)) {
                foreach ($files as $file) {
                    $extension = $file->extension();
                    $imageName = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();

                    $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $imageName);

                    $gallary = new ProductGallary();
                    $gallary->gallary_images = $imageName;


                    $gallary->product_id = $request->product_id;
                    $gallary->save();
                }
            }

            return response()->json(['status' => 1, 'msg' => "Product Image Gallary Created Successfully."]);
        }
    }

    public function gallaryImageDelete(Request $request)
    {
        $gallery = ProductGallary::findOrFail($request->id);
        $gallery->delete();
        File::delete(public_path('/uploads/products/' . $gallery->gallary_images));

        return response()->json(['status' => 1, 'msg' => "Gallary Image Deleted Successfully."]);
    }


    public function bulkExport()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function productSale()
    {
        $this->_hasMenuAccess('Reports-ProductSalesReport');
        return view('admin.products.sale');
    }

    public function getProductSaleCount(Request $request)
    {
        $this->_hasMenuAccess('Reports-ProductSalesReport');

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
        $totalRecords = Product::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Product::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        /*Fetch records*/
        if ($request->length == -1) {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('products.name', 'like', '%' . $searchValue . '%')
                ->select('id', 'name')
                ->take($rowperpage)
                ->get();
        } else {
            $records = Product::orderBy($columnName, $columnSortOrder)
                ->where('products.name', 'like', '%' . $searchValue . '%')
                ->select('id', 'name')
                ->skip($start)
                ->take($rowperpage)
                ->get();
        }

        $data_arr = array();

        $i = 1;
        $count = 0;
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            foreach ($record->orderDetails as $detail) {
                if ($record->id == $detail->product_id) {
                    if ($detail->delivery_status == 'sold') {
                        $count = $count + 1;
                    }
                }
            }


            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "total" => $count,
            );
            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        print(json_encode($response));
        exit();
    }

    public function deleted_product()
    {
        $this->_hasMenuAccess('ProductSetUp-delete');

        return view('admin.deleted_products.index');
    }

    public function getDeletedProduct(Request $request)
    {

        $this->_hasMenuAccess('ProductSetUp-delete');

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

        $basicQuery = "SELECT p.*, c.name as category_name,
        CASE
        When p.is_active = 1 Then 'Active'
        ELSE 'Not-Active'
        END As status
        From products p
        LEFT Join categories  c on c.id = p.category_id
        where p.is_active = 0";


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
                "slug" => $value->slug,
                "status" => $value->status,
                "category_name" => $value->category_name,
                'action' => "<a class='undo_products btn btn-primary btn-sm btn-rounded waves-effect waves-light' data-id='" . $value->id . "' href='javascript:'>Undo</a>",
                'checkbox' => "<input type='checkbox' class='multiple_undo'  id='multiple_undo-$value->id' name='multiple_delete' onchange='arrayProducts($value->id,this)' value='" . $value->id . "'>",

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

    function undoProducts(Request $request)
    {

        $this->_hasMenuAccess('ProductSetUp-delete');

        $product = Product::where('id', $request->deletedProductId)->first();

        $product->is_active = true;
        $product->save();

        foreach ($product->productAttributes as $attr) {
            $attr->is_active = true;
            $attr->save();
        }

        $maters = ProductMaster::where('product_id', $product->id)->latest()->first();
        $maters->is_active = true;
        $maters->save();

        $output = array(
            'response' => True,
            'msg' => " Product Updated Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    function deleteProducts(Request $request)
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $product = Product::where('id', $request->productId)->first();

        $product->is_active = false;
        $product->save();

        foreach ($product->productAttributes as $attr) {
            $attr->is_active = false;
            $attr->save();
        }

        $maters = ProductMaster::where('product_id', $product->id)->get();
        foreach ($maters as $item) {
            $item->is_active = false;
            $item->save();
        }

        $output = array(
            'response' => True,
            'msg' => "Product Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }

    public function deleteMultipleProducts(Request $request)
    {
        $this->_hasMenuAccess('ProductSetUp-delete');

        for ($i = 0; $i < count($request->id); $i++) {
            $product = Product::where('id', $request->id[$i])->first();

            $product->is_active = false;
            $product->save();

            foreach ($product->productAttributes as $attr) {
                $attr->is_active = false;
                $attr->save();
            }
            $maters = ProductMaster::where('product_id', $product->id)->get();
            foreach ($maters as $item) {
                $item->is_active = false;
                $item->save();
            }
        }

        $output = array(
            'response' => True,
            'msg' => " Products Delete Successfully",
            'result' => "",
        );

        return Response::json($output);
    }


    public function undoMultipleProducts(Request $request): JsonResponse
    {
        $this->_hasMenuAccess('ProductSetUp-delete');
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            DB::beginTransaction();
            for ($i = 0; $i < count($request->id); $i++) {
                $product = Product::where('id', $request->id[$i])->first();
                $product->is_active = true;
                $product->save();

                foreach ($product->productAttributes as $attr) {
                    $attr->is_active = true;
                    $attr->save();
                }

                $maters = ProductMaster::where('product_id', $product->id)->latest()->first();
                $maters->is_active = true;
                $maters->save();
                //                foreach ($maters as $item) {
                //                    $item->is_active = true;
                //                    $item->save();
                //                }
            }
            DB::commit();
            $output = array(
                'response' => True,
                'msg' => " Products undo Successfully",
                'result' => "",
            );
        } catch (Exception $e) {
            DB::rollBack();
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function add()
    {
        $attributes = Attribute::where('is_active', true)->get();
        $categories = Category::where('parent_id', 0)->where('is_active', 1)->with('children')->get();
        $subCategories = Category::where('parent_id', '!=', 0)->where('is_active', 1)->with('children')->get();
        $vendors = DB::table('vendors')->where('status', true)->get();
        return view('admin.products.add', compact('attributes', 'categories', 'subCategories', 'vendors'));
    }


    public function store(Request $request): JsonResponse
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'unique:products,name', 'max:255'],
                'category' => ['required', 'numeric'],
                'subCategory' => ['nullable', 'numeric'],
                'brand' => ['nullable', 'numeric'],
                'description' => ['required'],
                // 'specifications' => ['required'],
                //                'thum_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                'images' => ['nullable', 'array', 'max:5'],
                'images.*' => ['nullable', 'mimes:jpeg,png,jpg,JPG', 'max:2048'],
                'prod_sku' => [
                    'required',
                    'max:255',
                    Rule::unique('products', 'sku')->whereNot('products.is_active', false),
                    Rule::unique('product_attributes', 'sku')->whereNot('product_attributes.is_active', false)
                ],
                'prod_selling_price' => [Rule::requiredIf(!$request->attribute), 'numeric'],
                'prod_cost_price' => [Rule::requiredIf(!$request->attribute), 'numeric'],
                'prod_qty' => [Rule::requiredIf(!$request->attribute), 'numeric'],
            ]);

            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                DB::beginTransaction();

                $product = new Product();
                $product->brand_id = $request->brand;
                $product->name = $request->name;
                $product->slug = Str::slug($request->name, '-');
                $product->description = $request->description;
                // $product->specifications = $request->description;
                $product->category_id = $request->category;
                $product->sub_category_id = $request->subCategory;
                $today = today();
                $image_name = '';
                if ($request->has('thum_image')) {
                    $image_name = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $request->file('thum_image')->getClientOriginalName();
                    $request->file('thum_image')->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $image_name);
                }
                $imageName = $image_name;

                $product->image = $imageName;
                $product->vendor_id = $request->post('vendor');
                $product->sku = $request->prod_sku;
                $product->in_stock = true;
                $product->is_active = true;
                $product->created_at = now()->toDateString();
                $product->created_by = auth()->user()->id;
                $product->fiscal_year_id = getFiscalYear();
                $product->show_in_home = boolval($request->show_in_home);
                $product->top_rated = boolval($request->top_rated);
                $product->on_sale = boolval($request->on_sale);
                $product->is_featured = boolval($request->is_featured);
                $product->save();

                // Up to 5 product images; empty slots in the form arrive as null and are skipped.
                $galleryFiles = array_values(array_filter((array) $request->file('images')));
                foreach (array_slice($galleryFiles, 0, 5) as $file) {
                    $imageName = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();
                    $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $imageName);

                    // No thumbnail chosen: the first image becomes the main image (it already
                    // shows first on the product page, so it is not added to the gallery twice).
                    if (empty($product->image)) {
                        $product->image = $imageName;
                        $product->save();
                        continue;
                    }

                    $gallery = new ProductGallary();
                    $gallery->gallary_images = $imageName;
                    $gallery->product_id = $product->id;
                    $gallery->save();
                }


                if ($request->attribute) {
                    $attrValidaor = Validator::make($request->all(), [
                        'attribute_id' => ['required', 'array'],
                        'attribute_id.*' => ['required', 'integer'],
                        'attribute_value' => ['required', 'array'],
                        'attribute_value.*' => ['required', 'max:50'],
                        'sku' => ['required', 'array'],
                        'sku.*' => [
                            'required',
                            'max:255',
                            Rule::unique('products', 'sku')->whereNot('products.is_active', false),
                            Rule::unique('product_attributes', 'sku')->whereNot('product_attributes.is_active', false)
                        ],
                        'price' => ['required', 'array'],
                        'price.*' => ['required', 'numeric'],
                        'qty' => ['required', 'array'],
                        'qty.*' => ['required', 'integer'],
                    ]);

                    if ($attrValidaor->fails()) {
                        $output['result'] = $validator->errors()->toArray();
                        throw new Exception("Form Validation Error");
                    } else {

                        for ($i = 0; $i < sizeof($request->sku); $i++) {

                            if (isset($request->attribute_image[$i])) {

                                $file = $request->attribute_image[$i];
                                $image = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();
                                $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $image);
                            }

                            $attribute = new ProductAttribute();
                            $attribute->product_id = $product->id;
                            $attribute->attribute_id = $request->attribute_id[$i];
                            $attribute->value = $request->attribute_value[$i];
                            $attribute->sku = $request->sku[$i];
                            $attribute->featured_image = isset($request->attribute_image[$i]) ? $image : '';
                            $attribute->is_active = true;
                            $attribute->save();

                            $profit = $request->post('price')[$i] - $request->cost_price[$i];

                            //                            $profit_margin = number_format(($profit/$request->post('price')[$i]) * 100,2);


                            ProductMaster::create([
                                'product_id' => $product->id,
                                'attribute_id' => $attribute->id,
                                'cost_price' => $request->cost_price[$i],
                                'price' => $request->price[$i],
                                'profit_margin' => ($profit > 0) ? $profit : '0',
                                'quantity' => $request->qty[$i],
                                'is_active' => true
                            ]);
                        }
                    }
                } else {

                    $profit = $request->post('prod_selling_price') - $request->prod_cost_price;

                    //                    $profit_margin = number_format(($profit/$request->post('prod_selling_price')) * 100,2);

                    ProductMaster::create([
                        'product_id' => $product->id,
                        'attribute_id' => null,
                        'cost_price' => $request->prod_cost_price,
                        'price' => $request->prod_selling_price,
                        'profit_margin' => $profit > 0 ? $profit : '0',
                        'quantity' => $request->prod_qty,
                        'is_active' => true
                    ]);
                }
                DB::commit();
                $output = array(
                    'response' => true,
                    'msg' => "Product Added",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function edit($id)
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $product = Product::where([
            ['id', $id],
            ['is_active', true]
        ])->first();
        $categories = Category::where('parent_id', 0)->with('children')->get();
        $subCategories = Category::where('parent_id', '!=', 0)->with('children')->get();
        $brands = Brand::where('is_active', true)->get();
        $attributes = Attribute::where('is_active', true)->get();
        $productAttributes = ProductAttribute::where('product_id', $id)->where('is_active', 1)->get();
        $vendors = DB::table('vendors')->where('status', true)->get();

        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'brands', 'attributes', 'productAttributes', 'vendors'));
    }

    public function view(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $product = Product::where('id', $request->product_id)->first();
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = view('admin.products.view', compact('product'))->render();
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }
    public function update(Request $request): JsonResponse
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $product = Product::where([
                ['id', $request->id],
                ['is_active', true]
            ])->first();

            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'max:255',
                    Rule::unique('products', 'name')->ignore($product->id)
                ],
                'category' => ['required', 'numeric'],
                'subCategory' => ['nullable', 'numeric'],

                'brand' => ['nullable', 'numeric'],
                'description' => ['required'],
                // 'specifications' => ['required'],
                'thum_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                'images' => ['nullable', 'array'],
                'images.*' => ['nullable', 'mimes:jpeg,png,jpg,JPG', 'max:2048'],
            ]);

            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                DB::beginTransaction();
                $today = today();

                $product->brand_id = $request->brand;
                $product->name = $request->name;
                $product->slug = Str::slug($request->name, '-');
                $product->description = $request->description;
                // $product->specifications = $request->specifications;
                $product->category_id = $request->category;
                $product->sub_category_id = $request->subCategory;
                $product->vendor_id = $request->vendor;
                $product->created_by = Auth::user()->id;
                if ($request->file('thum_image')) {

                    if (File::exists(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m') . $product->image))) {
                        File::delete(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m') . $product->image));
                    }

                    $imageName = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $request->file('thum_image')->getClientOriginalName();
                    $request->file('thum_image')->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $imageName);

                    $product->image = $imageName;
                }
                $product->show_in_home = boolval($request->show_in_home);
                $product->top_rated = boolval($request->top_rated);
                $product->on_sale = boolval($request->on_sale);
                $product->is_featured = boolval($request->is_featured);
                $product->is_active = (bool) $request->status;
                $product->updated_at = now();
                $product->save();


                if ($request->file('images')) {
                    foreach ($request->file('images') as $file) {
                        $imageName = $today->format('Y') . '/' . $today->format('m') . '/' . strtotime(date('Y-m-d H:i:s')) . $file->getClientOriginalName();
                        $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $imageName);

                        $gallery = new ProductGallary();
                        $gallery->gallary_images = $imageName;
                        $gallery->product_id = $product->id;
                        $gallery->save();
                    }
                }

                if ($request->attributeCheckbox) {

                    if ($request->attribute) {
                        $attrValidaor = Validator::make($request->all(), [
                            'attributeId' => ['required', 'array'],
                            'attributeId.*' => ['required', 'integer'],
                            'attribute_id' => ['required', 'array'],
                            'attribute_id.*' => ['required', 'integer'],
                            'attribute_value' => ['required', 'array'],
                            'attribute_value.*' => ['required', 'max:50'],
                        ]);

                        if ($attrValidaor->fails()) {
                            $output['result'] = $attrValidaor->errors()->toArray();
                            throw new Exception("Form Validation Error");
                        } else {

                            for ($i = 0; $i < sizeof($request->attr_status); $i++) {

                                ProductMaster::where([
                                    ['attribute_id', $request->attributeId[$i]]
                                ])->update(['is_active' => false]);

                                $profit = $request->post('attr_selling_price')[$i] - $request->attr_cost_price[$i];

                                //                                $profit_margin = number_format(($profit/$request->post('attr_selling_price')[$i]) * 100,2);

                                ProductMaster::create([
                                    'product_id' => $request->id,
                                    'attribute_id' => $request->attributeId[$i],
                                    'cost_price' => $request->attr_cost_price[$i],
                                    'price' => $request->attr_selling_price[$i],
                                    'profit_margin' => $profit > 0 ? $profit : '0',
                                    'quantity' => $request->attr_qty[$i],
                                    'is_active' => true,
                                ]);

                                if (isset($request->attr_image[$i])) {
                                    $file = $request->attr_image[$i];
                                    $image = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();
                                    $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $image);
                                }

                                $attribute = ProductAttribute::find($request->attributeId[$i]);
                                $attribute->attribute_id = $request->attribute_id[$i];
                                $attribute->value = $request->attribute_value[$i];
                                $attribute->featured_image = isset($request->attr_image[$i]) ? $image : $attribute->featured_image;
                                $attribute->is_active = (bool) $request->attr_status[$i];
                                $attribute->save();
                            }
                        }
                    }
                } else {

                    if ($request->attribute) {
                        for ($i = 0; $i < sizeof($request->attributeId); $i++) {
                            $attribute = ProductAttribute::find($request->attributeId[$i]);
                            $attribute->is_active = false;
                            $attribute->save();
                        }
                        ProductMaster::where('product_id', $request->id)->update(['is_active' => 0]);
                        $productPriceValidator = Validator::make($request->all(), [
                            'prod_price' => ['required', 'numeric'],
                            'prod_qty' => ['required', 'integer']
                        ]);

                        if ($productPriceValidator->fails()) {
                            $output['result'] = $productPriceValidator->errors()->toArray();
                            throw new Exception("Form Validation Error");
                        } else {
                            ProductMaster::create([
                                'product_id' => $request->id,
                                'cost_price' => $request->cost_price,
                                'price' => $request->prod_price,
                                'quantity' => $request->prod_qty,
                                'is_active' => true,
                            ]);
                        }
                    } else {
                        $productPriceValidator = Validator::make($request->all(), [
                            'prod_price' => ['required', 'numeric'],
                            'prod_qty' => ['required', 'integer']
                        ]);

                        ProductMaster::where('product_id', $request->id)->update(['is_active' => 0]);
                        if ($productPriceValidator->fails()) {
                            $output['result'] = $productPriceValidator->errors()->toArray();
                            throw new Exception("Form Validation Error");
                        }

                        $profit = $request->post('prod_price') - $request->cost_price;

                        //                        $profit_margin = number_format(($profit/$request->post('prod_price')) * 100,2);

                        ProductMaster::create([
                            'product_id' => $request->id,
                            'cost_price' => $request->cost_price,
                            'price' => $request->prod_price,
                            'profit_margin' => $profit > 0 ? $profit : 0,
                            'quantity' => $request->prod_qty,
                            'is_active' => true,
                        ]);
                    }
                }

                DB::commit();
                $output = array(
                    'response' => true,
                    'msg' => "Product Added Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function getProductVariantForm(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $attributes = Attribute::where('is_active', true)->get();
            $product = Product::find($request->product_id);
            $View = view('admin.products.addVariantModal', compact('attributes', 'product'))->render();

            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $View,
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function postProductVariantForm(Request $request)
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'attribute_id' => ['required', 'integer'],
                'attribute_value' => ['required', 'max:50'],
                'sku' => [
                    'required',
                    'max:255',
                    Rule::unique('products', 'sku')->whereNot('products.is_active', false),
                    Rule::unique('product_attributes', 'sku')->whereNot('product_attributes.is_active', false)
                ],
                'price' => ['required', 'numeric'],
                'qty' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                DB::beginTransaction();

                $today = today();
                if ($request->has('attribute_image')) {
                    $file = $request->attribute_image;
                    $image = $today->format('Y') . '/' . $today->format('m') . '/' . date('m-d-Y_H-i-s') . $file->getClientOriginalName();
                    $file->move(public_path('/uploads/products/' . $today->format('Y') . '/' . $today->format('m')), $image);
                }
                $attr = ProductAttribute::create([
                    'product_id' => $request->product,
                    'attribute_id' => $request->attribute_id,
                    'value' => $request->attribute_value,
                    'sku' => $request->sku,
                    'featured_image' => $request->has('attribute_image') ? $image : '',
                    'is_active' => true,
                ]);
                $profit = $request->price - $request->cost_price;
                //               $profit_margin = number_format(($profit/$request->price) * 100,2);
                ProductMaster::create([
                    'product_id' => $request->product,
                    'attribute_id' => $attr->id,
                    'profit_margin' => $profit > 0 ? $profit : '0',
                    'cost_price' => $request->cost_price,
                    'price' => $request->price,
                    'quantity' => $request->qty,
                    'is_active' => true
                ]);

                DB::commit();

                $output = array(
                    'response' => true,
                    'msg' => "Product Attribute Added Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            DB::rollBack();
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function editProductBySKU()
    {
        $this->_hasMenuAccess('ProductSetup-Catalog');

        return view('admin.products.update');
    }

    public function searchProductSKU(Request $request): JsonResponse
    {
        $data = Product::where([
            ['sku', 'like', '%' . $request->keyword . '%'],
            ['is_active', true]
        ])->select('sku')
            ->get()
            ->toArray();

        $data1 = ProductAttribute::Where([
            ['sku', 'like', '%' . $request->keyword . '%'],
            ['is_active', true]
        ])->select('sku')
            ->get()
            ->toArray();


        $newData = array_merge($data, $data1);


        $result = array();
        if (sizeof($newData) > 0) {
            foreach ($newData as $value) {
                $row['id'] = $value['sku'];
                $row['text'] = $value['sku'];

                $result[] = $row;
            }
        }

        $output = array(
            'result' => array('results' => $result),
            'response' => true,
            'msg' => '',
        );

        return Response::json($output);
    }

    public function getProductSKUForm(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $sku = $request->sku;

            $product = Product::where([
                ['sku', $sku],
                ['is_active', true]
            ])
                ->Where('vendor_id', $request->vendor)
                ->orWhere('created_at', $request->date)
                ->first();
            $attribute = ProductAttribute::where([
                ['sku', $sku],
                ['is_active', true]
            ])->first();

            if ($attribute)
                $product = Product::find($attribute->product_id);

            if ($product) {
                $view = view('admin.products.updateProduct', compact('product', 'attribute'))->render();
            }

            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $view,
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function updateProductBySku(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );
        try {
            $validator = Validator::make($request->all(), [
                'product' => ['required', 'integer'],
                'attribute' => ['nullable', 'integer'],
                'price' => ['required', 'numeric'],
                'qty' => ['required', 'integer']
            ]);
            if ($validator->fails()) {
                $output['result'] = $validator->errors()->toArray();
                throw new Exception("Form Validation Error");
            } else {
                $attribute = $request->attribute ? $request->attribute : null;
                ProductMaster::create([
                    'product_id' => $request->product,
                    'attribute_id' => $attribute,
                    'price' => $request->price,
                    'quantity' => $request->qty,
                    'updated_at' => now(),
                    'is_active' => true,
                ]);

                $output = array(
                    'response' => true,
                    'msg' => "Product Updated Successfully Successfully",
                    'result' => "",
                );
            }
        } catch (Exception $e) {
            $output['response'] = false;
            $output['msg'] = $e->getMessage();
        }

        return Response::json($output);
    }

    public function getReportDetails(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {

            $product = Product::where([
                ['id', $request->id],
                ['is_active', true]
            ])->first();
            $records = ProductMaster::where([
                ['product_id', $product->id],
                ['is_active', true]
            ])->get();
            $view = view('admin.products.stockDetails', compact('records', 'product'))->render();
            $output = array(
                'response' => true,
                'msg' => "",
                'result' => $view,
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function deleteAttribute(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            ProductAttribute::where('id', $request->attribute_id)->delete();
            $output['response'] = true;
            $output['msg'] = 'Attribute deleted successfully';
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function get_product_attributes(Request $request)
    {
        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {

            $productAttributes = ProductAttribute::where('product_id', $request->product_id)->get();

            $arrayData = [];
            if ($productAttributes) {
                foreach ($productAttributes as $attribute) {
                    $arrayData[] = [
                        'atrribute_name' => $attribute->attribute->name . '(' . $attribute->value . ')',
                        'product_attribute_id' => $attribute->id,
                    ];
                }
            }
            $output['response'] = true;
            $output['msg'] = '';
            $output['result'] = $arrayData;
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }
    public function checkProductIsInStock(Request $request)
    {

        $output = [
            'response' => false,
            'result' => '',
            'msg' => ''
        ];

        try {


            $check_if_available = getStockQty($request->product_id, $request->attribute_id) - $request->qty;

            if ($check_if_available >= 0) {
                $output['response'] = true;
                $output['msg'] = '';
            } else {
                $output['response'] = false;
                $output['msg'] = 'product is out of stock';
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $output['msg'] = $exception->getMessage();
        }

        return Response::json($output);
    }

    public function updateProductAttribute(Request $request, $attribute_id)
    {
        dd($request->all(), $attribute_id);
    }
}
