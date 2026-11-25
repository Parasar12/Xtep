@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }} - Checkout</title>
@endsection
@section('content')

    @include('frontend.layouts.toastr')
    <style>
        .select2-selection__rendered {
            display: none !important;
        }
    </style>
    <!-- breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                                href="{{ route('front.index') }}">Home</a></li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Checkout
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <!-- End breadcrumb -->
    @php
        \Illuminate\Support\Facades\Session::forget('message');
    @endphp


    <div class="container">
        <div class="mb-5">
            <h1 class="text-center">Checkout</h1>
        </div>


        <!-- Accordion -->

        <div id="shopCartAccordion1" class="accordion rounded mb-6">
            <!-- Card -->

            <div class="card border-0">
                <div id="shopCartHeadingTwo" class="alert alert-primary mb-0" role="alert">
                    Have a coupon? <a href="#" class="alert-link" data-toggle="collapse" data-target="#shopCartTwo"
                                      aria-expanded="false" aria-controls="shopCartTwo">Click here to enter your
                        code</a>
                </div>
                <div id="shopCartTwo" class="collapse border border-top-0" aria-labelledby="shopCartHeadingTwo"
                     data-parent="#shopCartAccordion1" style="">
                    <form action="{{ route('cart.shipping') }}" method="get" autocomplete="off" id="quickOrder">
                        <input type="hidden" value="" name="total" id="total_amount">
                        <div class="row">
                            <div class="col-lg-6 item">
                                <div class="login-register-input-style input-style input-style-white">
                                    <label class=" required-label">Coupon Number</label>
                                    <input type="text" name="coupon" class="form-control"
                                           value="{{ \Illuminate\Support\Facades\Session::has('coupon') ? \Illuminate\Support\Facades\Session::get('coupon') : ($coupon ? $coupon->code : '') }}"
                                           required autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="float-right">
                                    <button class="btn btn-block btn-dark font-weight-normal btn-pill px-4">Apply
                                        Coupon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- End Card -->
        </div>


        <!-- End Accordion -->
        <form action="{{ route('orders.store') }}" method="post" id="order">
            @csrf
            <div class="row">
                <div class="col-lg-5 order-lg-2 mb-7 mb-lg-0">
                    <div class="pl-lg-3 ">
                        <div class="bg-gray-1 rounded-lg">
                            <!-- Order Summary -->
                            <div class="p-4 mb-4 checkout-table">
                                <!-- Title -->
                                <div class="border-bottom border-color-1 mb-5">
                                    <h3 class="section-title mb-0 pb-2 font-size-25">Your order</h3>
                                </div>
                                <!-- End Title -->
                                @if($coupon)

                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="product-name">Coupon Code</th>
                                            <th class="product-total">Total Coupon Discount</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="cart_item">
                                            <td>{{ $coupon->code }}</td>
                                            <td><span id="total_coupon_discount" class="price"></span></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                @endif
                                <div class="row">
                                    <div class="col-12">
                                        <div class="select-style billing-select mb-35">
                                            <label>Shipping Method *</label>
                                            <select class="form-control select-active" name="shipping" id="ShippingOpt"
                                                    required>
                                                @foreach($shippingMethods as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->title}} (Rs.{{ $item->cost }} )
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shipping')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="select-style billing-select mb-35">
                                            <label>Payment Method *</label>
                                            <select class="form-control select-active" name="payment_mode" required>
                                                @foreach($paymentOption as $item)
                                                    <option value="{{ $item->payment_mode_id }}">
                                                        {{ $item->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('payment_mode')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="order-summary">
                                    <div class="order-summary-title">
                                        <h3>Order summary</h3>
                                    </div>
                                    <div class="order-summary-top">

                                        @php
                                            $total = 0;
                                            $totalCouponDiscount = 0;
                                        @endphp

                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th class="product-name">Product</th>
                                                <th class="product-total">Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($cartList as $cart)
                                                @php
                                                    $couponDiscountedPrice = 0;
                                                    $productCouponDiscount = 0;
                                                    $couponPrice = 0;
                                                    $product = App\Models\Product::where('id',$cart->product_id)->first();
                                                    if(in_array($cart->product_id,getCouponProducts($coupon ? $coupon->id :
                                                    ''))){

                                                    $couponPrice =
                                                    getCouponDiscountedPrice($coupon->id,$product->id,$cart->quantity);
                                                    $couponDiscountedPrice = $cart->quantity * $couponPrice;
                                                    $productCouponDiscount = ($cart->price * $cart->quantity) -
                                                    ($couponDiscountedPrice);
                                                    $totalCouponDiscount += $productCouponDiscount;
                                                    }

                                                    if(in_array($cart->product_id,getCouponCategories($coupon ? $coupon->id :
                                                    ''))){
                                                    $couponPrice =
                                                    getCouponDiscountedPriceByCategory($coupon->id,$product->id,$cart->quantity);
                                                    $couponDiscountedPrice = $couponPrice;
                                                    $productCouponDiscount = ($cart->price * $cart->quantity) -
                                                    ($couponDiscountedPrice);
                                                    $totalCouponDiscount += $productCouponDiscount;
                                                    }

                                                    if($couponDiscountedPrice > 0){
                                                    $total += $couponDiscountedPrice;
                                                    }else{
                                                    $total += $cart->quantity * $cart->price;
                                                    }

                                                @endphp

                                                <input type="hidden" name="product_id[]"
                                                       value="{{ $cart->product_id }}">
                                                <input type="hidden" name="quantity[]" value="{{ $cart->quantity }}">
                                                <input type="hidden" name="price[]" value="{{ $cart->price}}">
                                                <input type="hidden" name="product_coupon_discount[]"
                                                       value="{{ $productCouponDiscount }}">
                                                <tr class="cart_item">
                                                    <td>{{ $product->name }} @if (!empty($cart->attribute))
                                                            @php $attribute =
                                                    \App\Models\ProductAttribute::find($cart->attribute);
                                                            @endphp
                                                            <a href="javascript:">
                                                                [{{ $attribute->attribute->name }}
                                                                :&nbsp;{{ $attribute->value }}]&nbsp;&nbsp;
                                                            </a>
                                                            <input type="hidden" name="attribute[]"
                                                                   value="{{ $cart->attribute }}">
                                                        @endif<strong class="product-quantity">× {{ $cart->quantity
                                                        }}</strong>
                                                    </td>
                                                    <td>Rs.{{ $couponDiscountedPrice > 0 ? $couponDiscountedPrice :
                                                    $cart->price *
                                                    $cart->quantity }}</td>
                                                </tr>

                                            @endforeach


                                            </tbody>
                                            <tfoot>
                                            @if($coupon)
                                                <th>Total Coupon Discount</th>
                                                <td>Rs.{{ $totalCouponDiscount }}</td>

                                            @endif
                                            <tr>
                                                <th>Subtotal</th>
                                                <td>Rs.{{ $total }}</td>
                                            </tr>
                                            <tr>
                                                <th>Shipping</th>
                                                <td><span id="ShippingSumPrice" class="price"></span></td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td><strong><span class="price" id="TotalAmount"></span></strong></td>
                                            </tr>
                                            </tfoot>
                                        </table>


                                    </div>
                                    <input type="hidden" name="coupon_id" value="{{ $coupon ? $coupon->id : ''}}">
                                    <input type="hidden" name="total_coupon_discount"
                                           value="{{ $coupon ? $totalCouponDiscount : '0' }}">
                                    <input type="hidden" name="sub_total" value="{{ $total }}">
                                    <input type="hidden" name="total_amount" id="final_amount" value="">

                                </div>

                                {{-- @php
                                $total = 0;
                                $totalCouponDiscount = 0;
                                @endphp
                                @foreach($cartList as $cart)
                                @php
                                $couponDiscountedPrice = 0;
                                $productCouponDiscount = 0;
                                $couponPrice = 0;
                                $product = App\Models\Product::where('id',$cart->product_id)->first();
                                if(in_array($cart->product_id,getCouponProducts($coupon ? $coupon->id : ''))){

                                $couponPrice = getCouponDiscountedPrice($coupon->id,$product->id,$cart->quantity);
                                $couponDiscountedPrice = $cart->quantity * $couponPrice;
                                $productCouponDiscount = ($cart->price * $cart->quantity) -
                                ($couponDiscountedPrice);
                                $totalCouponDiscount += $productCouponDiscount;
                                }

                                if(in_array($cart->product_id,getCouponCategories($coupon ? $coupon->id : ''))){
                                $couponPrice =
                                getCouponDiscountedPriceByCategory($coupon->id,$product->id,$cart->quantity);
                                $couponDiscountedPrice = $couponPrice;
                                $productCouponDiscount = ($cart->price * $cart->quantity) -
                                ($couponDiscountedPrice);
                                $totalCouponDiscount += $productCouponDiscount;
                                }

                                if($couponDiscountedPrice > 0){
                                $total += $couponDiscountedPrice;
                                }else{
                                $total += $cart->quantity * $cart->price;
                                }

                                @endphp

                                <input type="hidden" name="product_id[]" value="{{ $cart->product_id }}">
                                <input type="hidden" name="quantity[]" value="{{ $cart->quantity }}">
                                <input type="hidden" name="price[]" value="{{ $cart->price}}">
                                <input type="hidden" name="product_coupon_discount[]" value="{{ $productCouponDiscount }}">
                                <div class="order-summary-img-price">
                                    <div class="order-summary-img-title">
                                        <div class="order-summary-img">
                                            <a href="{{ route('front.product.details',$product->slug) }}">
                                                <img src="{{ asset('uploads/products/'.$product->image) }}" alt="">
                                            </a>
                                        </div>

                                        <div class="order-summary-title">
                                            <h4>{{ $product->name }}
                                                @if (!empty($cart->attribute))
                                                @php $attribute =
                                                \App\Models\ProductAttribute::find($cart->attribute);
                                                @endphp
                                                <a href="javascript:">
                                                    [{{ $attribute->attribute->name }}
                                                    :&nbsp;{{ $attribute->value }}]&nbsp;&nbsp;
                                                </a>
                                                <input type="hidden" name="attribute[]" value="{{ $cart->attribute }}">
                                                @endif
                                                <span>× {{ $cart->quantity }}</span>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="order-summary-price">
                                        <span>Rs.{{ $couponDiscountedPrice > 0 ? $couponDiscountedPrice :
                                            $cart->price *
                                            $cart->quantity }}</span>
                                    </div>
                                </div>
                                @endforeach


                                <input type="hidden" name="coupon_id" value="{{ $coupon ? $coupon->id : ''}}">
                                <input type="hidden" name="total_coupon_discount"
                                    value="{{ $coupon ? $totalCouponDiscount : '0' }}">
                                <input type="hidden" name="sub_total" value="{{ $total }}">
                                <input type="hidden" name="total_amount" id="final_amount" value="">
                                <div class="order-summary-middle">
                                    <ul>
                                        @if($coupon)
                                        <li>Total Coupon Discount <h4>Rs.{{ $totalCouponDiscount }}</h4>
                                        </li>
                                        @endif
                                        <li>Subtotal <h4>Rs.{{ $total }}</h4>
                                        </li>
                                        <li>Shipping <h4><span id="ShippingSumPrice" class="price"></span></h4>
                                        </li>
                                    </ul>
                                </div> --}}

                                @auth()

                                    <button type="submit" id="save"
                                            class="btn btn-primary-dark-w btn-block btn-pill font-size-20 mb-3 py-3">
                                        Place
                                        order
                                    </button>
                                @endauth


                            </div>
                            <!-- End Order Summary -->
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 order-lg-1">
                    <div class="pb-7 mb-7">
                        <!-- Title -->
                        <div class="border-bottom border-color-1 mb-5">
                            <h3 class="section-title mb-0 pb-2 font-size-25">Billing details</h3>
                        </div>
                        <!-- End Title -->

                        <!-- Billing Form -->
                        <div class="row">


                            <div class="col-md-12">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        State
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select-active" required name="province_id"
                                            id="Province">
                                        <option value="" selected="selected" disabled>Select State
                                        </option>
                                        <@foreach($provinces as $state)
                                            <option
                                                value="{{ $state->zone_state_id }}" {{ $addresses ? $state->
                                        zone_state_id == $addresses->province_id ? 'selected' : '' : ''
                                        }}>{{ $state->zone_state_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        District
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select-active" required name="district_id"
                                            id="District">
                                        <option value="" selected="selected" disabled>Select District
                                        </option>
                                    </select>
                                </div>
                                <!-- End Input -->
                            </div>

                            <div class="col-md-12">
                                <!-- Input -->
                                <div class="js-form-message mb-6">
                                    <label class="form-label">
                                        Street address
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="street" required
                                           placeholder="House number and street name"
                                           value="{{ $addresses ? $addresses->street : '' }}" type="text"
                                           data-msg="Please enter a valid address." data-error-class="u-has-error"
                                           data-success-class="u-has-success">
                                </div>
                                <!-- End Input -->
                            </div>

                            <div class="w-100"></div>
                        </div>
                        <!-- End Billing Form -->


                    </div>
                </div>
            </div>


        </form>
    </div>

@endsection

@section('script')
    <script>
        function calculateTotal(shiping, sub_total) {
            $.ajax({
                url: "{{ route('getShippingPrice') }}",
                type: "GET",
                data: {
                    id: shiping
                },
                success: function (response) {
                    if (response.response) {
                        const price = response.result
                        const total = Number(price) + Number(sub_total)
                        $('#TotalAmount').html('Rs.' + total)
                        $('#final_amount').val(total)
                        console.log($('#final_amount').val(total))
                        $('#ShippingSumPrice').html('Rs.' + price)
                    } else {
                        toastr.error(response.msg)
                        location.reload()
                    }
                }
            })
        }

        $(function () {
            @if($coupon)
            var total_coupon_discount = '{{ $totalCouponDiscount }}'
            $('#total_coupon_discount').html('Rs.' + total_coupon_discount)
            @endif
            var district_id
            @if($addresses)
                district_id = '{{ $addresses->district_id }}'
            @endif

            $('#order').on('submit', function (e) {

                var formData = new FormData(this);
                var orderUrl = $(this).attr('action');
                e.preventDefault()
                bootbox.confirm({
                    title: "Confirm Order ?",
                    message: "Do you want to confirm this Order ?",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancel'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Confirm'
                        }
                    },
                    callback: function (result) {
                        if (result) {
                            var data = formData
                            var url = orderUrl

                            window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {

                                location.href = responseData.result
                            }, function (error) {
                                toastr.error(error.msg);
                                window.ct.populateFormError("#update_feature", error.result);
                            });
                        } else {

                        }
                    }
                });
            });

            const shiping_price = $('#ShippingOpt').val()
            const sub_total = "{{ $total }}"
            $('#total_amount').val(sub_total)
            $('#ShippingOpt').on('change', function () {
                const shiping_price = $('#ShippingOpt').val()
                console.log(shiping_price)
                calculateTotal(shiping_price, sub_total)
            })
            calculateTotal(shiping_price, sub_total)

            $('#OTPVerify').hide()
            $('#UserRegister').hide()

            $('#SendOtpForm').submit(function (e) {
                e.preventDefault();
                $("#SendOtpForm").removeClass("has-error");
                $(".help-block").remove();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                }).done(function (resp) {
                    if (resp.status == false) {
                        $("#phone").addClass("has-error");
                        $("#phone").append(
                            '<div class="help-block text-danger">' + resp.message + "</div>"
                        )
                    }
                    if (resp.status == true) {
                        $('#OTPVerify').show()
                        $('#UserRegister').hide()
                        $('#PhoneNumber').hide()
                    }
                }).fail(function (resp) {
                    if (resp.responseJSON.errors.phone) {
                        $("#phone").addClass("has-error");
                        $("#phone").append(
                            '<div class="help-block text-danger">' + resp.responseJSON.errors.phone + "</div>"
                        )
                    }
                })
            })

            $('#verifyOtpForm').submit(function (e) {
                e.preventDefault();
                $("#verifyOtpForm").removeClass("has-error");
                $(".help-block").remove();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                }).done(function (resp) {
                    if (resp.status == false) {
                        $("#otp").addClass("has-error");
                        $("#otp").append(
                            '<div class="help-block text-danger">' + resp.message + "</div>"
                        )
                    }
                    if (resp.status == true) {
                        $('#OTPVerify').hide()
                        $('#UserRegister').show()
                        $('#PhoneNumber').hide()
                    }
                }).fail(function (resp) {
                    if (resp.responseJSON.errors.otp) {
                        $("#otp").addClass("has-error");
                        $("#otp").append(
                            '<div class="help-block text-danger">' + resp.responseJSON.errors.otp + "</div>"
                        )
                    }
                })
            })

            $('#createGuestUserForm').submit(function (e) {
                e.preventDefault();
                $("#createGuestUserForm").removeClass("has-error");
                $(".help-block").remove();
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                }).done(function () {
                    location.reload()
                }).fail(function (resp) {
                    if (resp.responseJSON.errors.email) {
                        $("#email").addClass("has-error");
                        $("#email").append(
                            '<div class="help-block text-danger">' + resp.responseJSON.errors.email + "</div>"
                        )
                    }
                    if (resp.responseJSON.errors.name) {
                        $("#name").addClass("has-error");
                        $("#name").append(
                            '<div class="help-block text-danger">' + resp.responseJSON.errors.name + "</div>"
                        )
                    }
                })
            })

            $('#Province').on('change', function () {
                var province_id = $(this).val();
                var DistrictDiv = $('#District');
                $.ajax({
                    url: "{{ route('getDistricts') }}",
                    method: "GET",
                    data: {
                        province_id
                    },
                    success: function (responseData) {
                        if (responseData.response) {

                            DistrictDiv.empty()

                            var districts = responseData.result

                            districts.map(district => {

                                var selected = (district_id == district.district_id) ? 'selected' : ''

                                DistrictDiv.append(`
                                   <option value="${district.district_id}" ${selected}>${district.district_name}
                                                    </option>
                                `)
                            })
                        }
                    }
                })
            })

            $('#Province').trigger('change');
        })
        $(document).ready(function () {
            // Initial setting of the shipping method based on the existing district
            var initialDistrictId = {{ $addresses->district_id }};
            setShippingMethod(initialDistrictId);

            $('#Province').on('change', function () {
                var province_id = $(this).val();
                var DistrictDiv = $('#District');
                $.ajax({
                    url: "{{ route('getDistricts') }}",
                    method: "GET",
                    data: {province_id: province_id},
                    success: function (responseData) {
                        if (responseData.response) {
                            DistrictDiv.empty();

                            var districts = responseData.result;

                            districts.map(district => {
                                var selected = (initialDistrictId == district.district_id) ? 'selected' : '';
                                DistrictDiv.append(`
                            <option value="${district.district_id}" ${selected}>${district.district_name}</option>
                        `);
                            });

                            // Set the shipping method for the initially selected district
                            setShippingMethod(DistrictDiv.val());

                        }
                    }
                });
            });

            $('#District').on('change', function () {
                var district_id = $(this).val();
                setShippingMethod(district_id);
            });

            function setShippingMethod(district_id) {
                var ShippingOpt = $('#ShippingOpt');
                console.log('change to district id', district_id);
                // Determine the shipping method based on district ID
                var shippingMethod = (district_id == 25 || district_id == 27 || district_id == 23) ? 1 : 2;

                // Set the value and disable the select box to prevent changes
                ShippingOpt.val(shippingMethod).prop('disabled', false);
            }
        });

    </script>
@endsection
