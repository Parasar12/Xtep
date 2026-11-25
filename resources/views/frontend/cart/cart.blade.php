@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Cart</title>
@endsection
@section('content')

<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Cart</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<!-- End breadcrumb -->
@php $is_cart = 0; @endphp
<div class="container">
    <div class="mb-4">
        <h1 class="text-center">Cart</h1>
    </div>
    <div class="mb-10 cart-table">
        <form class="mb-4" action="#" method="post">
            <table class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-remove">&nbsp;</th>
                        <th class="product-thumbnail">&nbsp;</th>
                        <th class="product-name">Product</th>
                        <th class="product-price">Price</th>
                        <th class="product-quantity w-lg-15">Quantity</th>
                        <th class="product-subtotal">SubTotal</th>
                        <th class="product-remove">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @if (Auth::check())
                    @php
                    $total = 0;
                    @endphp
                    @if($cartList->count())
                    @php $is_cart = 1; @endphp
                    @foreach ($cartList as $cartProduct)
                    @php
                    $product = App\Models\Product::where('id', $cartProduct->product_id)->first();
                    $total += $cartProduct->price * $cartProduct->quantity;
                    @endphp
                    <tr id="table-item-{{ $cartProduct->product_id }}">
                        <td class="text-center">
                            <a onclick="removeCart({{ $cartProduct->product_id }})"
                                class="text-gray-32 font-size-26 cursor-pointer-on">×</a>
                        </td>

                        <td class="d-none d-md-table-cell">
                            <a href="{{ route('front.product.details', $product->slug) }}"><img
                                    class="img-fluid max-width-100 p-1 border border-color-1"
                                    src="{{ asset('uploads/products/' . $product->image) }}"
                                    alt="{{$product->name}}"></a>
                        </td>

                        <td data-title="Product">
                            <a class="text-gray-90" href="{{ route('front.product.details', $product->slug) }}">
                                {{ $product->name }}</a>
                            @if (!empty($cartProduct->attribute))
                            @php $attribute =
                            \App\Models\ProductAttribute::find($cartProduct->attribute); @endphp
                            <a href="javascript:">[{{ $attribute->attribute->name }}
                                :&nbsp;{{ $attribute->value }}]&nbsp;&nbsp;</a>
                            @endif
                        </td>

                        <td data-title="Price">
                            <span class=""> Rs.{{ $cartProduct->price }}</span>
                        </td>

                        <td data-title="Quantity">
                            <span class="sr-only">Quantity</span>
                            <!-- Quantity -->
                            <div class="border rounded-pill py-1 width-122 w-xl-80 px-3 border-color-1">
                                <div class="js-quantity row align-items-center">
                                    <div class="col">

                                        <input class="js-result form-control h-auto border-0 rounded p-0 shadow-none"
                                            id="quantity-{{ $cartProduct->product_id }}"
                                            name="quantity-{{ $cartProduct->product_id }}"
                                            value="{{ $cartProduct->quantity }}">
                                    </div>

                                </div>
                            </div>
                            <!-- End Quantity -->
                        </td>

                        <td data-title="Total">
                            <span class="">Rs.
                                <span id="product_total-{{ $cartProduct->product_id }}">
                                    {{ !empty($cartProduct->attribute) ?
                                    getProductVariantPrice($cartProduct->product_id,
                                    $cartProduct->attribute) * $cartProduct->quantity
                                    :$cartProduct->price * $cartProduct->quantity }}
                                </span></span>
                        </td>
                        <td class="text-center">
                            {{-- <a onclick="updateCart({{ $cartProduct->product_id }})"
                                class="  btn btn-primary-dark-w ">Update
                                cart</a> --}}
                                <a onclick="updateCart({{ $cartProduct->product_id }})"
                                    class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto ">Update Cart</a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    @php $is_cart = 0; @endphp
                    <tr>
                        <td colspan="7" class="text-center">No Item Found</td>
                    </tr>
                    @endif
                    @endif
                    @if (Session::has('cart'))
                    @php $is_cart = 1; @endphp
                    @php
                    $total = 0;
                    @endphp

                    @foreach ($cartList as $cartProduct)
                    {{-- @dd($cartList) --}}
                    @php
                    $product = App\Models\Product::where('id', $cartProduct->product_id)->first();
                    $total += $cartProduct->price * $cartProduct->quantity;
                    @endphp
                    <tr id="table-item-{{ $cartProduct->product_id }}">
                        <td class="text-center">
                            <a onclick="removeCart({{ $cartProduct->product_id }})"
                                class="text-gray-32 font-size-26 cursor-pointer-on">×</a>
                        </td>

                        <td class="d-none d-md-table-cell">
                            <a href="{{ route('front.product.details', $product->slug) }}"><img
                                    class="img-fluid max-width-100 p-1 border border-color-1"
                                    src="{{ asset('uploads/products/' . $product->image) }}"
                                    alt="{{$product->name}}"></a>
                        </td>

                        <td data-title="Product">
                            <a class="text-gray-90" href="{{ route('front.product.details', $product->slug) }}">
                                {{ $product->name }}</a>
                            @if (!empty($cartProduct->attribute))
                            @php $attribute =
                            \App\Models\ProductAttribute::find($cartProduct->attribute); @endphp
                            <a href="javascript:">[{{ $attribute->attribute->name }}
                                :&nbsp;{{ $attribute->value }}]&nbsp;&nbsp;</a>
                            @endif
                        </td>

                        <td data-title="Price">
                            <span class=""> Rs.{{ $cartProduct->price }}</span>
                        </td>

                        <td data-title="Quantity">
                            <span class="sr-only">Quantity</span>
                            <!-- Quantity -->
                            <div class="border rounded-pill py-1 width-122 w-xl-80 px-3 border-color-1">
                                <div class="js-quantity row align-items-center">
                                    <div class="col">

                                        <input class="js-result form-control h-auto border-0 rounded p-0 shadow-none"
                                            id="quantity-{{ $cartProduct->product_id }}"
                                            name="quantity-{{ $cartProduct->product_id }}"
                                            value="{{ $cartProduct->quantity }}">
                                    </div>

                                </div>
                            </div>
                            <!-- End Quantity -->
                        </td>

                        <td data-title="Total">
                            <span class="">Rs.
                                <span id="product_total-{{ $cartProduct->product_id }}">
                                    {{ !empty($cartProduct->attribute) ?
                                    getProductVariantPrice($cartProduct->product_id,
                                    $cartProduct->attribute) * $cartProduct->quantity
                                    :$cartProduct->price * $cartProduct->quantity }}
                                </span></span>
                        </td>
                        <td class="text-center">
                            {{-- <a onclick="updateCart({{ $cartProduct->product_id }})"
                                class="btn btn-primary-dark-w ">Update
                                cart</a> --}}
                                <a onclick="updateCart({{ $cartProduct->product_id }})"
                                class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto ">Update Cart</a>
                        </td>
                        
                    </tr>
                    @endforeach




                    @endif
                    <tr>
                        <td colspan="6" class="border-top space-top-2 justify-content-center">
                            <div class="pt-md-3">
                                <div class="d-block d-md-flex flex-center-between">
                                    <div class="mb-3 mb-md-0 w-xl-40">
                                        <!-- Apply coupon Form -->

                                    </div>
                                    <div class="d-md-flex">
                                        <button type="button" onclick="clearCart()"
                                            class="btn btn-soft-secondary mb-3 mb-md-0 font-weight-normal px-5 px-md-4 px-lg-5 w-100 w-md-auto">Clear
                                            Cart</button>

                                        @auth

                                        <a href="{{ route('cart.shipping') }}"
                                            class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-none d-md-inline-block">Proceed
                                            to checkout</a>
                                        @else

                                        <a href="{{ url('user/order-request') }}"
                                            class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-none d-md-inline-block">Proceed
                                            to checkout</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    @if($is_cart == 1)
    <div class="mb-8 cart-total">
        <div class="row">
            <div class="col-xl-5 col-lg-6 offset-lg-6 offset-xl-7 col-md-8 offset-md-4">
                <div class="border-bottom border-color-1 mb-3">
                    <h3 class="d-inline-block section-title mb-0 pb-2 font-size-26">Cart totals</h3>
                </div>
                <table class="table mb-3 mb-md-0">
                    <tbody>
                        <tr class="cart-subtotal">
                            <th>Subtotal</th>
                            <td data-title="Subtotal"><span id="sub_total" class="amount">Rs.{{ $total }}</span></td>
                        </tr>

                        <tr class="order-total">
                            <th>Total</th>
                            <td data-title="Total"><strong><span id="total_price" class="amount">Rs.{{ $total
                                        }}</span></strong></td>
                        </tr>
                    </tbody>
                </table>
                @auth
                <a href="{{ route('cart.shipping') }}"
                    class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-none">Proceed to
                    checkout</a>
                @else
                <a href="{{ url('user/order-request') }}"
                    class="btn btn-primary-dark-w ml-md-2 px-5 px-md-4 px-lg-5 w-100 w-md-auto d-md-none">Proceed to
                    checkout</a>
                @endauth
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@section('script')
<script>
    function updateCart(product_id) {
            const quantity = $('#quantity-' + product_id).val();
            $.ajax({
                url: "{{ route('cart.update') }}",
                method: "post",
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    if (data.status == 0) {
                        toastr.error(data.error)
                    } else {
                        toastr.success(data.msg)
                        singleProductPrice(product_id)
                        totalInCart()

                    }


                }
            })
        }


        function removeCart(id) {
            $.ajax({
                url: "{{ route('cart.remove') }}",
                method: "get",
                data: {
                    id: id,
                },
                success: function (data) {
                    if (data.status == 0) {
                        toastr.error(data.error)
                    } else {

                        totalInCart()
                        cartCount();
                        loadCartItem();
                        toastr.success(data.msg)
                        $('#table-item-' + id).remove();
                    }
                }
            })
        }

        function singleProductPrice(product_id) {

            $.ajax({
                url: "{{ route('cart.singleProductPrice') }}",
                method: "get",
                data: {
                    product_id: product_id,
                },
                success: function (data) {
                    console.log(data)
                    $('#product_total-' + product_id).html(data);
                }
            })
        }

        function totalInCart() {
            $.ajax({
                url: "{{ route('cart.totalPrice') }}",
                method: "get",
                success: function (data) {
                    $('#total_price').html('Rs.' + data);
                    $('#sub_total').html('Rs.' +data);
                }
            })
        }

        function clearCart() {
            $.ajax({
                url: "{{ route('cart.clear') }}",
                method: "post",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (data) {
                    if (data.status == 0) {

                    } else {
                        location.reload()
                        loadCart();
                        cartCount();
                        toastr.success(data.msg)
                    }
                }

            })
        }
</script>
@endsection