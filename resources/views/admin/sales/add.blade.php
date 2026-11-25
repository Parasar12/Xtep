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
                                            <h4 class="text-primary">Order Process</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('sales_entry.store') }}" method="post"
                                          id="addQuotationForm" enctype="multipart/form-data"
                                          class="eq-section" novalidate>
                                        @csrf

                                        <div class="row mb-4">
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group item mb-3">
                                                    <label for="CustomerName" class="required-label">Customer Name</label>
                                                    <select name="customer" id="CustomerName" class="form-control" required></select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-6 col-sm-12 mt-3">
                                                <button type="button" class="btn btn-outline-warning" onclick="openCustomerAddModal()">Add Customer</button>
                                            </div>

                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="form-group item mb-3">
                                                    <label for="customerAddressId" class="required-label">Customer Address</label>
                                                    <select name="shipping_address_id" id="customerAddressId" class="form-control" required></select>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 col-md-6 col-sm-12 mt-3">
                                                <button type="button" class="btn btn-outline-warning" onclick="openCustomerAddressModal()">Add Customer Address</button>
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
                                                        <option value="0" selected>NO</option>
                                                        <option value="13">13%</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="couponCode">Coupon Number</label>
                                                    <input type="text" id="couponCode" name="coupon_code" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="datepicker" class="required-label">Delivery Estimated Date And Tune</label>
                                                    <input  type="text" id="datepicker" data-date-format="d M, Y h:i K" class="form-control" name="estimated_date"  required readonly>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-12" id="items">
                                                <table class="table table-bordered nowrap compact" width="100%" id="quotationItemsTbl">
                                                    <thead>
                                                    <tr>
                                                        <th class="required-label">Particulars</th>
                                                        <th class="required-label">Attribute/Variant</th>
                                                        <th style="width: 140px;" class="required-label">Quantity</th>
                                                        <th style="width: 160px;" class="required-label">Unit Price</th>
                                                        <th style="width: 160px;" class="required-label">Coupon Discount</th>
                                                        <th style="width: 160px;" class="required-label">Total Price</th>
                                                        <th style="width: 115px;"></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="quotationItemsTblBody">
                                                    <tr>
                                                        <td class="item">
                                                            <select style="width: 100%;" class="form-control particulars" required=""
                                                                    name="particulars[]">
                                                            </select>
                                                        </td>
                                                        <td class="item">
                                                            <select style="width: 100%;" class="form-control variants"
                                                                    name="variants[]">
                                                            </select>
                                                        </td>
                                                        <td class="item">
                                                            <input type="number" placeholder="Quantity" required="" name="quantity[]"
                                                                   class="form-control quantity">
                                                        </td>
                                                        <td class="item">
                                                            <input type="number" required="" placeholder="Unit Price" name="unitPrice[]"
                                                                   class="form-control unitPrice">
                                                        </td>
                                                        <td class="item">
                                                            <input type="number" required="" value="0" placeholder="coupon discount" name="couponDiscount[]"
                                                                   class="form-control couponDiscount">
                                                        </td>
                                                        <td class="item">
                                                            <input type="number" required="" placeholder="Total Price" readonly="" name="totalPrice[]"
                                                                   class="form-control totalPrice">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="add_items_row btn btn-success btn-sm pull-left"><i
                                                                    class="bx bx-plus-circle"></i></button>&nbsp;
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="deliveryVendor">Delivery Logistic Vendor </label>
                                                    <select class="form-control" id="deliveryVendor" name="delivery_vendor">
                                                        <option value="" selected disabled>select delivery vendor</option>
                                                        @foreach($delivery_vendors as $vendor)
                                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2" id="delivery_vendor_amount">
                                                <label>Delivery Charge to Customer</label>
                                                <input id="delivery_amount" type="text"  value="0.00" class="form-control" name="delivery_vendor_amount_to_customer">
                                            </div>
                                            <div class="col-lg-2" id="delivery_vendor_amount">
                                                <label>Delivery Charge by Logistic Vendor</label>
                                                <input id="delivery_amount" type="text"  value="0.00" class="form-control" name="delivery_vendor_amount">
                                            </div>
                                            <div class="col-lg-2 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="orderMode">Order By </label>
                                                    <select class="form-control" id="orderMode" name="mode_type_id" required>
                                                        <option value="" selected disabled>select order by</option>
                                                        @foreach($orderModes as $mode)
                                                            <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-lg-2 col-md-6 col-sm-12">
                                                <div class="form-group item mb-3">
                                                    <label for="orderBy" class="required-label">Sales By</label>
                                                    <select name="order_by" id="orderBy" class="form-control" required>
                                                        <option value="" selected disabled>select ordery by</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}"> {{ $user->name .'('. $user->email .')'  }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-12">
                                                <div class="form-group item">
                                                    <label for="discountType">Discount Type </label>
                                                    <select class="form-control" id="discountType" name="discountType">
                                                        <option value="" selected disabled></option>
                                                        <option value="percent">Percentage (%)</option>
                                                        <option value="amount">Fixed Amount</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-4 col-sm-12" id="percent" style="display: none;">
                                                <div class="form-group item">
                                                    <label class="required-label">Discount Percentage (%) </label>
                                                    <input type="number" class="form-control" name="discount_per" id="discount_per"
                                                           placeholder="Discount %">
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-6 col-sm-12" id="disAmt" style="display: none;">
                                                <div class="form-group item">
                                                    <label class="required-label">Discount Amount </label>
                                                    <input type="number" class="form-control" name="discount_amt" id="discount_amt"
                                                           placeholder="Discount Amount" required="">
                                                </div>
                                            </div>

                                            <div class="col-lg-4">
                                                <div class="form-group item">
                                                    <label for="orderStatus" class="required-label">Order Status </label>
                                                    <select class="form-control" id="orderStatus" name="order_status" required>
                                                        @foreach($order_status as $status)
                                                            <option value="{{ $status->id }}" {{ $status->id == 1 ? 'selected' : '' }}>{{ $status->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
{{--                                            <div class="col-lg-3 col-md-4 col-sm-12">--}}
{{--                                                <div class="form-group item">--}}
{{--                                                    <label for="discountType" class="required-label">Shipping Method</label>--}}
{{--                                                    <select class="form-control" id="shippingMethod" name="shipping_method" required>--}}
{{--                                                        <option value="" selected disabled>select shipping method</option>--}}
{{--                                                       @foreach($shippings as $ship)--}}
{{--                                                           <option value="{{ $ship->id }}">{{ $ship->title.'('.  $ship->duration .')'}}</option>--}}
{{--                                                       @endforeach--}}
{{--                                                    </select>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <div class="col-lg-3 col-md-4 col-sm-12" id="shipping_amount">--}}

{{--                                            </div>--}}
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Basic Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="basic_amount" value="0" disabled=""
                                                           readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Discount Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="quo_discount_amount" value="0" disabled=""
                                                    <input type="text" class="form-control input-sm" id="quo_discount_amount" value="0" disabled=""
                                                           readonly="readonly">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label class="required-label">Tax Amount :</label>
                                                    <input type="text" readonly="readonly" value="0" class="form-control input-sm" name="term_amount"
                                                           id="term_amount" placeholder="Tax Amount">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group item">
                                                    <label>Net Amount :</label>
                                                    <input type="text" class="form-control input-sm" id="net_amount" value="0" disabled=""
                                                           readonly="readonly">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-lg-6">
                                                <div class="form-group item">
                                                    <label for="remarks_by_customer"> Remarks by Customer:</label>
                                                    <textarea class="form-control" id="remarks_by_customer" name="customer_remarks"></textarea>
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
        <div class="modal fade" tabindex="-1" id="OrderModal" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" id="OrderModalContent"></div>
            </div>
        </div>
    </div>
    <!-- END layout-wrapper -->
@endsection

@section('script')
    <script>
        $(function () {
            initSalesNoteSelectBox()

            flatpickr("#datepicker", {

                // Other Flatpickr options here
                minDate: "today",
                enableTime: true,
                // altInput: true,
                // dateFormat: "Y-m-d H:i",

            });

            sessionStorage.setItem('coupon_id','')
            $('#orderBy').select2()
            $('#customerAddressId').select2()
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

                $.ajax({
                    url:"{{ route('getCustomerAddresses') }}",
                    method:"GET",
                    data:{
                        'user_id' : $(this).val()
                    },
                    success:function(responseData){
                        if(responseData.response){
                            const addresses = responseData.result
                            $('#customerAddressId').empty()
                            addresses.map(address=>{
                                const option = new Option(address.user_address,address.address_id);
                                $('#customerAddressId').append(option)
                            })
                        }
                    }
                })
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
            $('#orderMode').select2()
            $('#discountType').select2({
                allowClear: true,
                placeholder: "Select Discount Type",
            }).val(null).trigger('change').change(function () {
                var disType = $(this).val();

                $('#discount_per').val('');
                $('#discount_amt').val('');
                $('#quo_discount_amount').val(0)
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

                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
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

            $(document).on('keyup change blur', '#quotationItemsTblBody .quantity', function () {
                var qty = $(this).val();
                var rate = $(this).parent().parent().find('.unitPrice').val();
                var product_id = $(this).parent().parent().find('.particulars').val();
                var attribute_id = $(this).parent().parent().find('.variants').val();
                var div = $(this).parent().parent();

                const coupon_id  = sessionStorage.getItem('coupon_id')

                $.ajax({
                    url:"{{ route('checkProductIsInStock') }}",
                    data:{
                        product_id,
                        qty,
                        attribute_id
                    },
                    success:function (responseData){
                        if (responseData.response){

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
                                div.find('.totalPrice').val(calculateRateAmount(qty, 'quantity', rate));

                                calculateBillAmount($('#quotationItemsTblBody tr'));
                                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                                calculateShippingAmount();
                                calculateVendorCharge();

                            }

                        }
                        else{
                            toastr.error(responseData.msg)
                        }
                    }
                })

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

                        const variantMainDiv =  div.find('.variants');

                    $.ajax({
                        method:"GET",
                        url:"{{ route('get_product_attributes') }}",
                        data:{
                            product_id
                        },
                        success:function (responseData){
                            if(responseData.response){
                                variantMainDiv.empty()
                                const attributes = responseData.result

                                if(attributes.length){
                                    variantMainDiv.append(`

                                    <option selected disabled>select attribute</option>
                                    `)
                                    attributes.map(attribute=>{
                                        variantMainDiv.append(`

                                        <option value="${attribute.product_attribute_id}"> ${attribute.atrribute_name}</option>
                                        `)
                                    })
                                }else{
                                    variantMainDiv.append(`
                                    <option value="" >no attributes found</option>
                                    `)

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
                            }
                            div.find('.unitPrice').val(responseData.result);
                        }
                    })
                }
            });

            $('#quotationItemsTblBody').on('change', '.variants', function () {

                var attribute = $(this).val();
                var div = $(this).parent().parent();
                var product = div.find('.particulars').val()
                if(attribute > 0){
                    $.ajax({
                        method:"GET",
                        url:"{{ route('getProductAttributePrice') }}",
                        data:{
                            product,
                            attribute
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

            $('#couponCode').on('change',function (){
                checkCoupon()
            })

            $('#delivery_amount').on('keyup change blur', function () {
                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();
            });

            $('#checkVendorCharge').on('click',function (){

                calculateBillAmount($('#quotationItemsTblBody tr'));
                calculateDiscountAmount($('#quotationItemsTblBody tr'));
                calculateShippingAmount();
                calculateVendorCharge();

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

            let net_amount = 0.00
            const vendor_amount = $('#delivery_amount').val()
             net_amount = $('#net_amount').val()

            if(vendor_amount)
                $('#net_amount').val( (parseFloat(vendor_amount) + parseFloat(net_amount)).toFixed(2))
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

            $('.variants').select2()
        }

        function openCustomerAddModal(){
            $('#OrderModal #OrderModalContent').html($('#customer_template').html());
            $("#OrderModal").modal('show');
        }

        function openCustomerAddressModal(){
            $('#OrderModal #OrderModalContent').html($('#customer_address_template').html());

            $('#customer_id').val($('#CustomerName').val())
            $("#OrderModal").modal('show');
        }
    </script>
    <script type="text/template" id="particulars_row">
        <tr>
            <td class="item">
                <select style="width: 100%;" class="form-control particulars" required name="particulars[]">
                </select>
            </td>
            <td class="item">
                <select style="width: 100%;" class="form-control variants"
                        name="variants[]">
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
    <template id="customer_template">
        <div class="modal-header">
            <h5 class="modal-title">Add Customer Info</h5>
        </div>

        <div class="modal-body">
            <form method="post" action="{{route('save_user_from_order')}}" class="eq-section" novalidate id="FAQForm">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <div class="col-md-6 item">
                                        <label for="name" class="col-form-label required-label">Customer Name</label>
                                       <input type="text" required id="name" name="name" class="form-control">
                                    </div>
                                    <div class="col-md-6 item">
                                        <label for="contactNumber" class="col-form-label required-label">Contact Number </label>
                                        <input type="text" required id="contactNumber" name="contact_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 item">
                                        <label for="secondaryContactNumber" class="col-form-label required-label">Secondary Contact Number</label>
                                        <input type="text" required id="secondaryContactNumber" name="secondary_contact" class="form-control">
                                    </div>

                                    <div class="col-md-6 item">
                                        <label for="email" class="col-form-label required-label">Email</label>
                                        <input type="email" required id="email" name="email" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" style="float: left;" id="save"
                                               class="btn btn-primary waves-effect waves-light" value="Submit">
                                        <button type="button" class="btn btn-danger ml-3"
                                                data-bs-dismiss="modal">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
            </form>
        </div>

        {{--@section('script')--}}
        <script>
            $(function () {
                $("#FAQForm").submit(function (event) {
                    if (validator.checkAll($(this))) {
                        const data = new FormData(this)
                        const url = $(this).attr('action')

                        event.preventDefault();
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        });
                        window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                            toastr.success(responseData.msg);
                            $('#OrderModal').modal('hide');
                            const data = responseData.result

                            const option = new Option(data.name,data.id);
                            $('#CustomerName').append(option)

                        }, function (error) {
                            toastr.error(error.msg);
                            window.ct.populateFormError("#FAQForm", error.result);
                        });
                    }
                    return false;
                })
            })

        </script>
        {{--@endsection--}}

    </template>


    <template id="customer_address_template">
        <div class="modal-header">
            <h5 class="modal-title">Add Customer Address</h5>
        </div>

        <div class="modal-body">
            <form method="post" action="{{route('add_customer_address')}}" class="eq-section" novalidate id="customer_address_post">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <input type="hidden" name="customer_address_id" id="customer_id">

                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group item mb-3">
                                            <label for="province" class="required-label">Province</label>
                                            <select name="province_id" id="province" class="form-control" required>
                                                <option selected value="" disabled>select province</option>
                                                @foreach($provinces as $province)
                                                    <option value="{{ $province->zone_state_id }}">{{ $province->zone_state_name }}</option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-group item mb-3">
                                            <label for="district" class="required-label">District</label>
                                            <select name="district_id" id="district" class="form-control" required>
                                                <option selected value="" disabled>select district</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 item">
                                        <label for="address" class="col-form-label required-label">address</label>
                                        <input type="text" required id="address" name="address" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" style="float: left;" id="save"
                                               class="btn btn-primary waves-effect waves-light" value="Submit">
                                        <button type="button" class="btn btn-danger ml-3"
                                                data-bs-dismiss="modal">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div>
            </form>
        </div>

        {{--@section('script')--}}
        <script>
            $(function () {

                $('#customer').select2({
                    minimumInputLength: 2,
                    allowClear: true,
                    dropdownParent : $('#OrderModal'),
                    placeholder: "Search Customer By Name",
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

                $('#province').select2({
                    dropdownParent : $('#OrderModal')
                })
                $('#district').select2({
                    dropdownParent : $('#OrderModal')
                })

                $('#province').on('change',function (){

                    $.ajax({
                        url:"{{ route('getDistricts') }}",
                        method:"GET",
                        data :{
                            'province_id':$(this).val()
                        },
                        success:function (responseData){
                            const districts = responseData.result
                            const mainDiv = $('#district')
                            console.log(districts.length)
                            if(districts.length > 0){
                                mainDiv.html('')
                                districts.map(district=>{
                                    mainDiv.append(`
                                       <option value="${district.district_id}">${district.district_name}</option>
                                `)
                                })
                            }else
                                mainDiv.html(`
                                   <option value="" disabled>no districts found</option>
                                `)
                        }
                    })
                })

                $("#customer_address_post").submit(function (event) {
                    if (validator.checkAll($(this))) {
                        const data = new FormData(this)
                        const url = $(this).attr('action')

                        event.preventDefault();
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        });
                        window.ct.postDataMultiPartForm(url, data, $("#save")).then(function (responseData) {
                            toastr.success(responseData.msg);
                            $('#OrderModal').modal('hide');
                            const data = responseData.result
                            const option = new Option(data.user_address,data.address_id);
                            $('#customerAddressId').append(option)

                        }, function (error) {
                            toastr.error(error.msg);
                            window.ct.populateFormError("#customer_address_post", error.result);
                        });
                    }
                    return false;
                })
            })

        </script>
        {{--@endsection--}}

    </template>
@endsection

