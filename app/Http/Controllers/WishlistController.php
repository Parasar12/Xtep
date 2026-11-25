<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Response;

class WishlistController extends Controller
{

    public function index()
    {
        $this->clearQuickOrderData();
        $wishlists = Wishlist::where('user_id', auth()->user()->id)->get();
        return view('frontend.wishlist', compact('wishlists'));
    }

    public function store(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            $wishlist = new Wishlist();
            $wishlist->product_id = $request->id;
            $wishlist->user_id = auth()->id();
            $wishlist->save();

            $product = Product::find($request->id);
            $view = view('frontend.component.wishList', compact('product'))->render();
            $wishlishtIconView = view('frontend.component.header_wishlist')->render();

            $output = array(
                'response' => true,
                'msg' => $product->name . ' Added to Wishlist Successfully!',
                'result' => $view,
                'wishlist'=>$wishlishtIconView
            );

        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function remove(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            Wishlist::where('user_id', auth()->id())->where('product_id', $request->id)->delete();
            $product = Product::find($request->id);
            $view = view('frontend.component.wishList', compact('product'))->render();
            $wishlishtIconView = view('frontend.component.header_wishlist')->render();
            $output = array(
                'response' => true,
                'msg' => $product->name . "Removed From Wishlist Successfully",
                'result' => $view,
                'wishlist'=>$wishlishtIconView
            );
        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function removeList(Request $request): JsonResponse
    {
        $output = array(
            'response' => false,
            'msg' => "Something went wrong. Please try again later.",
            'result' => "",
        );

        try {
            Wishlist::where('user_id', auth()->id())->where('product_id', $request->id)->delete();
            $product = Product::find($request->id);
            $wishlists = Wishlist::where('user_id', auth()->id())->get();
            $view = view('frontend.component.wishlist_table', compact('wishlists'))->render();

            $output = array(
                'response' => true,
                'msg' => $product->name . "Removed From Wishlist Successfully",
                'result' => $view,
            );

        } catch (Exception $e) {
            $output['msg'] = $e->getMessage();
        }
        return Response::json($output);
    }

    public function productInWishlist()
    {
        $this->_hasMenuAccess('Report-Wishlist');

        return view('admin.wishlist.index');
    }

    public function getProductWishlistCount(Request $request)
    {
        $this->_hasMenuAccess('Report-Wishlist');

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
        foreach ($records as $record) {
            $id = $record->id;
            $name = $record->name;
            $total = $record->wishlists->count();


            $data_arr[] = array(
                "id" => $i,
                "name" => $name,
                "total" => $total,
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
}
