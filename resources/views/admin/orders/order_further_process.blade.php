@extends('layouts.app')
@section('content')
    <style>
        input[type=text], input[type=number] {
            height: 30px;
        }

        .ck-editor__editable_inline {
            min-height: 200px;
        }
    </style>
    <div id="layout-wrapper">
        @include('admin.includes.header')
        @include('admin.includes.left_sidebar')
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="float-right">
                                                <a href="{{ route('orders.index') }}" class="btn btn-outline-danger">
                                                    <i class="fa fa-backward">Back</i>
                                                </a>
                                            </div>
                                            <h4 class="text-primary">Further Process</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('orders.updateFurtherProcess') }}" method="post"
                                          id="addQuotationForm" enctype="multipart/form-data"
                                          class="eq-section" novalidate>
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <div class="row mb-4">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group item mb-3">
                                                    <label for="CustomerName" class="required-label">Customer Name</label>
                                                    <select name="customer" id="CustomerName" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="ValidTill" class="required-label">Bill Date</label>
                                                    <input id="bill_date" type="date" class="form-control"
                                                           name="bill_date" data-provider="flatpickr" value="{{ $order->date}}" data-date-format="Y-M-d" required>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mb-4">
                                            {{--                                            <div class="col-lg-4 col-md-6 col-sm-12">--}}
                                            {{--                                                <div class="form-group item">--}}
                                            {{--                                                    <label class="required-label ">Payment Type</label>--}}
                                            {{--                                                    <select name="payment_type" id="payment_type" class="form-control" required>--}}
                                            {{--                                                        <option selected disabled value="">Select Payment Type</option>--}}
                                            {{--                                                        <option value="One Time" >One Time</option>--}}
                                            {{--                                                        <option value="Monthly" >Monthly</option>--}}
                                            {{--                                                        <option value="Yearly" >Yearly</option>--}}
                                            {{--                                                    </select>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                            {{--                                            <div class="col-lg-4 col-md-6 col-sm-12">--}}
                                            {{--                                                <div class="form-group item">--}}
                                            {{--                                                    <label class="required-label">Category</label>--}}
                                            {{--                                                    <select name="category_id" id="category" class="form-control" required>--}}
                                            {{--                                                        <option selected disabled value="">Select Category Type</option>--}}
                                            {{--                                                        @foreach($categories as $category)--}}
                                            {{--                                                            <option value="{{ $category->id }}" >{{ $category->name }}</option>--}}
                                            {{--                                                        @endforeach--}}
                                            {{--                                                    </select>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="vat_type">VAT</label>
                                                    <select name="vat" id="vat_type" class="form-control">
                                                        <option value="0" {{ $order->Vat == '0' ? 'selected' : '' }}>NO</option>
                                                        <option value="13" {{ $order->vat == '13' ? 'selected' : '' }}>13%</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="couponCode">Coupon Number</label>
                                                    <input type="text" id="couponCode" name="coupon_code" value="{{ $order->coupon_code }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-12" id="items">
                                                <table class="table table-bordered nowrap compact" width="100%" id="quotationItemsTbl">
                                                    <thead>
                                                    <tr>
                                                        <th class="required-label">Particulars</th>
                                                        <th style="width: 140px;" class="required-label">Quantity</th>
                                                        <th style="width: 160px;" class="required-label">Unit Price</th>
                                                        <th style="width: 160px;" class="required-label">Coupon Discount</th>
                                                        <th style="width: 160px;" class="required-label">Total Price</th>
{{--                                                        <th style="width: 115px;"></th>--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody id="quotationItemsTblBody">
                                                    @php
                                                        $basic_amount = 0;
                                                    @endphp
                                                    @foreach($orderDetails as $detail)

                                                        @php

                                                          $total_price = ($detail->price * $detail->quantity) - $detail->product_coupon_discount;

                                                          $basic_amount +=$total_price;
                                                        @endphp
                                                        <tr>
                                                            <td class="item">
                                                                <select style="width: 100%;" class="form-control particulars" required=""
                                                                        name="particulars[]" >
                                                                    <option  value="{{ $detail->product_id }}"> {{ $detail->product->name }}</option>
                                                                </select>
                                                            </td>
                                                            <td class="item">
                                                                <input type="number" placeholder="Quantity" required="" name="quantity[]"
                                                                       class="form-control quantity" value="{{ $detail->quantity }}">
                                                            </td>
                                                            <td class="item">
                                                                <input type="number" required="" placeholder="Unit Price" name="unitPrice[]"
                                                                       class="form-control unitPrice" value="{{ $detail->price }}">
                                                            </td>
                                                            <td class="item">
                                                                <input type="number" required="" value="{{ $detail->product_coupon_discount }}" placeholder="coupon discount" name="couponDiscount[]"
                                                                       class="form-control couponDiscount">
                                                            </td>
                                                            <td class="item">
                                                                <input type="number" required="" placeholder="Total Price" readonly="" name="totalPrice[]"
                                                                       class="form-control totalPrice" value="{{ $total_price }}">
                                                            </td>
                                                            {{--                                                        <td>--}}
                                                            {{--                                                            <button type="button" class="add_items_row btn btn-success btn-sm pull-left"><i--}}
                                                            {{--                                                                    class="bx bx-plus-circle"></i></button>&nbsp;--}}
                                                            {{--                                                        </td>--}}
                                                        </tr>
                                                    @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="deliveryVendor" class="required-label"> Delivery Vendor </label>
                                                    <select class="form-control" id="deliveryVendor" name="delivery_vendor" required>
                                                        <option value="" selected disabled>select delivery vendor</option>
                                                        @foreach($delivery_vendors as $vendor)
                                                            <option value="{{ $vendor->id }}" {{ $vendor->id == $order->delivery_vendor_id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4" id="delivery_vendor_amount">

                                                <label>Delivery Vendor Amount</label>
                                                <input id="delivery_amount" type="text"  value="{{ $order->delivery_vendor_amount ?  $order->delivery_vendor_amount : 0 }}" class="form-control" name="delivery_vendor_amount">

                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group item">

                                                    @php
                                                        if ($order->delivery_status == '3' || $order->delivery_status == '4' || $order->delivery_status == '6' || $order->delivery_status == '7' || $order->delivery_status == '8')
                                                            $status = 'disabled';
                                                        else
                                                            $status = '';
                                                    @endphp
                                                    <label for="orderStatus" class="required-label">Order Status </label>
                                                    <select class="form-control" id="orderStatus" name="order_status" required {{ $status }}>
                                                        <option value="" selected disabled>select order status</option>
                                                        @foreach($order_status as $status)
                                                            <option value="{{ $status->id }}" {{ $order->delivery_status == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="discountType">Discount Type </label>
                                                    <select class="form-control" id="discountType" name="discountType">
                                                        <option value="" selected disabled></option>
                                                        <option value="percent" {{ $order->discountType == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                                                        <option value="amount" {{ $order->discountType == 'amount' ? 'selected' : '' }}>Fixed Amount</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-12" id="percent" style="display: none;">
                                                <div class="form-group item">
                                                    <label class="required-label">Discount Percentage (%) </label>
                                                    <input type="number" class="form-control" name="discount_per" id="discount_per"
                                                           placeholder="Discount %" value="{{ $order->discount_per }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12" id="disAmt" style="display: none;">
                                                <div class="form-group item">
                                                    <label class="required-label">Discount Amount </label>
                                                    <input type="number" class="form-control" name="discount_amt" id="discount_amt"
                                                           placeholder="Discount Amount" required="" value="{{ $order->discount_amount }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="discountType" class="required-label">Shipping Method</label>
                                                    <select class="form-control" id="shippingMethod" name="shipping_method" required>
                                                        <option value="" selected disabled>select shipping method</option>
                                                        @foreach($shippings as $ship)
                                                            <option value="{{ $ship->id }}" {{ $ship->id == $order->shipping_method_id ? 'selected' :'' }}>{{ $ship->title.'('.  $ship->duration .')'}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-12" id="shipping_amount">
                                                @if($order->shipping_method_id)
                                                    <label>Shipping Amount</label>
                                                    <input type="text" readonly value="{{ getShipingPrice($order->shipping_method_id) }}" class="form-control">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Basic Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="basic_amount" value="{{ $order->grand_total }}" disabled=""
                                                           readonly="readonly" >
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Discount Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="quo_discount_amount" value="{{ $order->discount_amount }}" disabled=""
                                                           readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label class="required-label">Tax Amount :</label>
                                                    <input type="text" readonly="readonly" value="{{ $order->tax_amount }}" class="form-control input-sm" name="term_amount"
                                                           id="term_amount" placeholder="Tax Amount">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Net Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="net_amount" value="{{ $order->net_total }}" disabled=""
                                                           readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="gap-2 justify-content-end">

                                                <button type="submit" class="btn btn-success me-5" id="save">Save</button>
                                                <a href="{{ route('orders.index') }}">
                                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Back</button></a>
                                                <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- END layout-wrapper -->
@endsection

@section('script')
    <script>
        $(function () {
            initSalesNoteSelectBox()

            sessionStorage.setItem('coupon_id','')

            var today = new Date().toISOString().split('T')[0];
            $('input[type="date"]').attr('min', today);

            $("#addQuotationForm").submit(function (event) {
                if (validator.checkAll($(this))) {
                    const data =  new FormData(this)
                    const url = $(this).attr('action')

                    event.preventDefault();

                    window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                        toastr.success(responseData.msg);
                        location.replace("{{ route('orders.index') }}")
                    }, function (error) {
                        toastr.error(error.msg);
                        window.ct.populateFormError("#addQuotationForm", error.result)
                    })
                }
                return false
            })
            // $("#bill_date").flatpickr()
            // $("#due_date").flatpickr()
            $('#deliveryVendor').select2()

            const option = new Option('{{ $order->user->name }}','{{ $order->user_id }}');
            $('#CustomerName').append(option)
            $('#CustomerName').select2({
                minimumInputLength: 2,
                allowClear: true,
                placeholder: "Search Customer or Vendor By Name",
                ajax: {
                    delay: 350,
                    transport: function (params, success, failure) {
                        window.ct.getData("{{ route('searchCustomer') }}", [{
                            name: 'q',
                            value: params.data.term
                        }], false).then(function (responseData) {
                            success(responseData.result);
                        }, function (error) {
                            $('#select2-CustomerName-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                        });
                    }
                }
            })

            $('#CustomerName').on('change',function (){
                checkCoupon()
            })
            $('#payment_type').select2();
            $('#orderStatus').select2();
            $('#category').select2();
            $('#vat_type').select2({
                allowClear: true,
                placeholder: "Select VAT Type",
            }).val('0').trigger('change').change(function () {
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            })
            $('#shippingMethod').select2()
            $('#discountType').select2({
                allowClear: true,
                placeholder: "Select Discount Type",
            }).val(null).trigger('change').change(function () {
                var disType = $(this).val();

                $('#discount_per').val('');
                $('#discount_amt').val('');

                if (disType) {
                    if ('percent' == disType) {
                        $('#percent').fadeIn();
                        $('#disAmt').fadeOut();
                        $('#discount_per').prop('required', true);
                        $('#discount_amt').prop('required', false);
                    }

                    if ('amount' == disType) {
                        $('#disAmt').fadeIn();
                        $('#percent').fadeOut();
                        $('#discount_amt').prop('required', true);
                        $('#discount_per').prop('required', false);
                    }
                } else {
                    $('#disAmt').fadeOut();
                    $('#percent').fadeOut();
                    $('#discount_amt').prop('required', false);
                    $('#discount_per').prop('required', false);
                }
            })

            $('#discount_per, #discount_amt').on('keyup change blur', function () {
                var dis_type = $('#discountType').val();

                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();

            })

            $('#shippingMethod').on('change',function (){

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            })
            $('#deliveryVendor').on('change',function (){

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            })
            $("#items").on('click', '.add_items_row', function (event) {
                $("#quotationItemsTbl tbody").append($("#particulars_row").html());
                initSalesNoteSelectBox();

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            })

            $("#items").on('click', '.del_items_row', function (event) {
                $(this).parent().parent().remove();

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            })

            $('#quotationItemsTblBody').on('keyup change blur', '.quantity', function () {
                var qty = $(this).val();
                var rate = $(this).parent().parent().find('.unitPrice').val();
                var product_id = $(this).parent().parent().find('.particulars').val();
                var div = $(this).parent().parent();

                const coupon_id  = sessionStorage.getItem('coupon_id')

                if(coupon_id && qty){
                    $.ajax({
                        url:"{{ route('getCouponDiscountPrice') }}",
                        method:"GET",
                        data:{
                            coupon_id,
                            qty,
                            product_id
                        },
                        success:function (responseData){
                            const item = responseData.result
                            div.find('.couponDiscount').val(item.total_coupon_discount)
                            div.find('.totalPrice').val(item.total_price)

                            calculateBillAmount($('#quotationItemsTblBody tr'));
                            calculateDiscountAmount($('#quotationItemsTblBody tr'));
                            calculateShippingAmount();
                            calculateVendorCharge();
                        }
                    })
                }else{
                    $(this).parent().parent().find('.totalPrice').val(calculateRateAmount(qty, 'quantity', rate));

                    calculateBillAmount($('#quotationItemsTblBody tr'));
                    calculateDiscountAmount($('#quotationItemsTblBody tr'));
                    calculateShippingAmount();
                    calculateVendorCharge();

                }

            });

            $('#quotationItemsTblBody').on('keyup change blur', '.unitPrice', function () {
                var qty = $(this).parent().parent().find('.quantity').val();
                var rate = $(this).val();
                $(this).parent().parent().find('.totalPrice').val(calculateRateAmount(qty, 'rate', rate));

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();


            });

            $('#quotationItemsTblBody').on('change', '.particulars', function () {

                var product_id = $(this).val();
                var div = $(this).parent().parent();

                if(product_id > 0){
                    $.ajax({
                        method:"GET",
                        url:"{{ route('getProductPrice') }}",
                        data:{
                            product_id
                        },
                        success:function (responseData){
                            div.find('.unitPrice').val(responseData.result);
                        }
                    })
                }
            });
            $('#quotationItemsTblBody').on('keyup change blur', '.totalPrice', function () {
                var qty = $(this).parent().parent().find('.qty').val();
                var amount = $(this).val();
                $(this).parent().parent().find('.rate').val(calculateRateAmount(qty, 'amount', amount));

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            });

            $('#term_amount').on('keyup change blur', function () {
                var taxAmt = parseFloat($(this).val());
                $('#net_amount').val((taxAmt + parseFloat($('#basic_amount').val())).toFixed(2));

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            });
            $('#delivery_amount').on('keyup change blur', function () {
                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            });
            $('#couponCode').on('change',function (){
                checkCoupon()
            })
        })

        function checkCoupon(){
            const coupon_code = $('#couponCode').val()
            if(coupon_code){
                const user_id = $('#CustomerName').val()
                const data = {
                    coupon_code,
                    user_id
                }

                $.ajax({
                    url:"{{ route('check_coupon_code') }}",
                    method:"GET",
                    data:data,
                    success:function (responseData){
                        if(responseData.response){
                            sessionStorage.setItem('coupon_id',responseData.result)
                            toastr.success(responseData.msg)
                        }else{
                            toastr.error(responseData.msg)
                            sessionStorage.removeItem('coupon_id')
                        }
                    }
                })
            }
        }

        function calculateRateAmount(qty, calculateFlag, rateAmount) {
            var total = 0.0000;
            if ('rate' == calculateFlag || 'quantity' == calculateFlag) {
                total = (parseFloat(qty || 0) * parseFloat(rateAmount || 0)).toFixed(2);
            } else if ('amount' == calculateFlag) {
                total = (parseFloat(rateAmount || 0) / parseFloat(qty || 0)).toFixed(2);
            }
            return total;
        }

        function calculateVendorCharge(){

            const net_amount = $('#net_amount').val()
            const vendor_amount = $('#delivery_amount').val()
            console.log(vendor_amount)
            if(vendor_amount)
                $('#net_amount').val( parseFloat(vendor_amount) + parseFloat(net_amount))
        }
        function calculateShippingAmount(){

            var shipping = {{ Js::from($shippings) }};

            const ship_id = $('#shippingMethod').val();
            const ship = shipping.find(pp => pp.id == ship_id)
            if (ship){
                $('#shipping_amount').html(`
                              <label>Shipping Amount</label>
                             <input type="text" readonly value="${ship.cost}" class="form-control">
            `)
                const total_amount = $('#net_amount').val()
                $('#net_amount').val( parseFloat(ship.cost) + parseFloat(total_amount))
            }
        }
        function calculateBillAmount(rowObject) {
            var basicAmt = 0.0000;
            $.each(rowObject, function (index, val) {
                basicAmt += (parseFloat($(val).find('.totalPrice').val()) || 0);
            });

            var amount_without_vat = basicAmt - (parseFloat($('#addQuotationForm #quo_discount_amount').val()) || 0);
            $('#basic_amount').val(basicAmt.toFixed(2));

            var vat_type = $('#vat_type').val();
            $('#term_amount').val('0');

            if ('13' == vat_type) {
                var vat = (13 / 100) * amount_without_vat;
                var amount_with_vat = amount_without_vat + vat;
                $('#term_amount').val(vat);
                $('#net_amount').val((parseFloat(amount_with_vat)).toFixed(2));

            }

            if ('0' == vat_type || null == vat_type || '' == vat_type) {
                $('#term_amount').val(0);
                $('#net_amount').val(((parseFloat(0)) + amount_without_vat).toFixed(2));
            }
        }

        function calculateDiscountAmount(rowObject) {
            var basicAmt = 0.0000;
            var discount = 0.0000;

            var type = $('#discountType').val();

            if (null != type || '' != type) {

                $.each(rowObject, function (index, val) {
                    basicAmt += (parseFloat($(val).find('.totalPrice').val()) || 0);
                });

                if ('percent' == type) {
                    d_percent = $('#addQuotationForm #discount_per').val();
                    disAmt = d_percent / 100 * basicAmt;
                }

                if ('amount' == type) {
                    disAmt = $('#addQuotationForm #discount_amt').val();
                }

                var discount = (parseFloat(disAmt) || 0);
                $('#quo_discount_amount').val(discount.toFixed(2));
            } else {
                $('#quo_discount_amount').val(discount.toFixed(2));
            }
        }

        function initSalesNoteSelectBox() {
            $("#addQuotationForm #quotationItemsTbl #quotationItemsTblBody .particulars").select2({
                placeholder: "Search Product Name",
                minimumInputLength: 2,
                allowClear: true,
                ajax: {
                    delay: 350,
                    transport: function (params, success, failure) {
                        window.ct.getData("{{ route('product.searchProducts') }}", [{
                            name: 'q',
                            value: params.data.term
                        }], false).then(function (responseData) {
                                success(responseData.result);
                            },
                            function (error) {
                                $('#select2-particulars-results').html('<li role="treeitem" aria-live="assertive" class="select2-results__option select2-results__message">' + error.msg + '</li>');
                            });
                    }
                }
            });
        }
    </script>
    <script type="text/template" id="particulars_row">
        <tr>
            <td class="item">
                <select style="width: 100%;" class="form-control particulars" required name="particulars[]">
                </select>
            </td>
            <td class="item">
                <input type="number" placeholder="Quantity" required name="quantity[]" class="form-control quantity">
            </td>
            <td class="item">
                <input type="number" placeholder="Unit Price"
                       required
                       name="unitPrice[]" class="form-control unitPrice">
            </td>
            <td class="item">
                <input type="number" value="0"
                       required
                       name="couponDiscount[]" class="form-control couponDiscount">
            </td>
            <td class="item">
                <input type="number" required placeholder="Total Price" name="totalPrice[]" readonly
                       class="form-control totalPrice">
            </td>
            <td>
                <button type="button" class="add_items_row btn btn-success btn-sm pull-left"><i class=" bx bx-plus-circle"></i>
                </button>&nbsp;
                <button type="button" class="del_items_row btn btn-danger btn-sm"><i class=" bx bx-minus-circle"></i></button>
            </td>
        </tr>
    </script>
@endsection

