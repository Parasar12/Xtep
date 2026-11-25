@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }} - OrderList</title>
@endsection
@section('content')
    <div class="breadcrumb-area breadcrumb-area-padding-2 bg-gray-2">
        <div class="custom-container">
            <div class="breadcrumb-content text-center">
                <ul>
                    <li>
                        <a href="{{ route('front.index') }}">Home</a>
                    </li>
                    <li class="active">Order Date Wise</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="wishlist-area pt-75 pb-75">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="wishlist-table-content" id="orders_list">
                        <div class="table-content table-responsive">
                            <table>
                                <tbody>
                                @if($orders->count())
                                    @php
                                        $total_products = 0;
                                    @endphp
                                    @foreach ($orders as $order)

                                        @php
                                        $total = 0;
                                            $total_order = \App\Models\Order::where([
                                                ['user_id',\auth()->user()->id],
                                                ['created_at',$order->created_at]
                                            ])->get();
                                            foreach ($total_order as $item){
                                                $total += $item->orderDetails->count();
                                            }
                                          foreach ($total_order as $val){
                                              $total_products += intval($val->orderDetails[0]->quantity);
                                          }
                                        @endphp
                                        <tr style="background-color: #f6f1f1">

                                            <td class="wishlist-product-img" style="width: 20%">
                                                <h4> <b>{{ date('Y-m-d',strtotime($order->created_at)) }}</b></h4>
                                            </td>

                                            <td class="wishlist-product-add-wrap">
                                                <h4>Total Order: {{  $total }}</h4>
                                            </td>
                                            <td class="wishlist-product-add-wrap">
                                                <div class="wishlist-product-add">
                                                    <a onclick="viewOrders('{{$order->created_at}}')">View
                                                        Orders</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">No Order's Found</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function viewOrders(order_date){
            const orderDiv = $('#orders_list')
            $.ajax({
                url:"{{ route('view_orders') }}",
                method : "GET",
                data :{
                    order_date
                },
                success:function (responseData){
                    orderDiv.html('')
                    orderDiv.html(responseData.result);
                },
                error: function (responseData){
                    toastr.error(responseData.msg)
                }
            })
        }

        function backToDateWiseOrders(){
            const orderDiv = $('#orders_list')
            $.ajax({
                url:"{{ route('back_to_main_order_page') }}",
                method : "GET",

                success:function (responseData){
                    orderDiv.html('')
                    orderDiv.html(responseData.result);
                },
                error: function (responseData){
                    toastr.error(responseData.msg)
                }
            })
        }


        function closeModal(){

            $('#addBannerModal').modal('hide');
        }
    </script>
@endsection
