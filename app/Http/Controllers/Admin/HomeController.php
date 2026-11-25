<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductMaster;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller{
    
    public function __construct(){
        $this->middleware('admin');
    }
    
    public function index(){

        $shippingEarnings = 0;
        $orders = Order::all();
        foreach ($orders as $order) {
            if ($order->shippingMethod) {
                $shippingEarnings = $shippingEarnings + $order->shippingMethod->cost;
            }
        }

        $revenue = Order::all()->sum('grand_total');

        $pendingCount = OrderDetail::where('delivery_status', 1)->count();
        $processingCount = OrderDetail::where('delivery_status', 4)->count();
        $deliveredCount = OrderDetail::where('delivery_status', 5)->count();
        $cancelledCount = OrderDetail::where('delivery_status', 2)->count();
        $returnedCount = OrderDetail::where('delivery_status', 7)->count();

        $mostOrderedProducts = DB::table('products')
            ->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
            ->selectRaw('products.*, COALESCE(sum(order_details.quantity),0) total')
            ->groupBy('products.id')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get();

        $topUsers = DB::table('users')
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->selectRaw('users.*, COALESCE(sum(orders.grand_total),0) total')
            ->groupBy('users.id')
            ->orderBy('total', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('pendingCount', 'processingCount', 'deliveredCount', 'cancelledCount', 'returnedCount', 'revenue', 'shippingEarnings', 'mostOrderedProducts', 'topUsers'));
    }
}
