@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Order Confirmation</title>
@endsection
@section('content')
@include('frontend.layouts.toastr')

<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Order Details
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<div class="container">
    <div class="mb-4">
        <h1 class="text-center">Confirmed Order</h1>
    </div>
    <div class="mb-10 cart-table">
        <table class="table" cellspacing="0">
            <thead>
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <th class="product-name">Product</th>
                    <th class="product-price">Price</th>
                    <th class="product-quantity w-lg-15">Quantity</th>
                    <th class="product-subtotal">SubTotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $data)
                <tr id="table-item-{{ $data->id }}">

                    <td class="d-none d-md-table-cell">
                        <a href="{{ route('front.product.details', $data->product->slug) }}"><img
                                class="img-fluid max-width-100 p-1 border border-color-1"
                                src="{{ asset('uploads/products/' .  $data->product->image) }}"
                                alt="{{ $data->product->name}}"></a>
                    </td>

                    <td data-title="Product">
                        <a href="{{ route('front.product.details', $data->product->slug) }}" class="text-gray-90">
                            {{ $data->product->name }}
                        </a>
                        @if ($data->attribute != null)
                        @php $attribute = \App\Models\ProductAttribute::find($data->attribute);
                        @endphp
                        @if($attribute)
                        <a href="javascript:">
                            [{{ $attribute->attribute->name }}
                            :&nbsp;{{ $attribute->value }}]&nbsp;&nbsp;</a>
                        @endif
                        @endif
                    </td>

                    <td data-title="Price">
                        <span class="amount">Rs.{{ $data->price }}</span>
                    </td>

                    <td data-title="Quantity">
                        <span>
                            {{ $data->quantity }}
                        </span>
                        <!-- End Quantity -->
                    </td>

                    <td data-title="Total">
                        @if($data->product_coupon_discount > 0)
                        @php
                        $price = $data->price - $data->product_coupon_discount;
                        $total = $price * $data->quantity
                        @endphp
                        <span>Rs. {{ $total }} </span>

                        @else
                        <span>Rs. {{ $data->price * $data->quantity }} </span>
                        @endif


                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div class="mb-8 cart-total">
        <div class="row">
            <div class="col-xl-5 col-lg-6 offset-lg-6 offset-xl-7 col-md-8 offset-md-4">
                <div class="border-bottom border-color-1 mb-3">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">Cart totals</h3>
                </div>
                <table class="table mb-3 mb-md-0">
                    <tbody>
                        @if($order->total_coupon_discount > 0)
                        <tr>
                            <td>Total Coupon Discount</td>
                            <td>Rs.{{ $order->total_coupon_discount }}</td>
                        </tr>
                        @endif
                        <tr class="cart-subtotal">
                            <th>Subtotal</th>
                            <td data-title="Subtotal"><span id="sub_total" class="amount">Rs.{{
                                    $order->grand_total }}</span></td>
                        </tr>
                        <tr class="cart-subtotal">
                            <th>Shipping</th>
                            <td data-title="Shipping"><span id="sub_total" class="amount">
                                    Rs.{{ getShipingPrice($order->shipping_method_id) }}
                                </span></td>
                        </tr>
                        <tr class="order-total">
                            <th>Total</th>
                            <td data-title="Total"><strong><span class="amount"> Rs.{{ $order->net_total
                                        }}</span></strong></td>
                        </tr>
                    </tbody>
                </table>
                @auth
                <a href="{{ route('cart.shipping') }}"
                    class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-none">Proceed to
                    checkout</a>
                @else
                <a href="{{ route('user.login') }}"
                    class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-none">Proceed to
                    checkout</a>
                @endauth
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
@endsection