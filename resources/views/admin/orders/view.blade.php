@extends('layouts.app')

@section('content')

    @include('admin.includes.header')

    @include('admin.includes.left_sidebar')
    <!-- Begin page -->
    <div id="layout-wrapper">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-2">
                    </div>

                    <div class="col-lg-10">
                        <div class="card">
                            <div class="card-body">

                                <!-- Quotation Starts From Here -->
                                <div class="invoice-title" id="printArea">
                                    <div class="mb-4">
                                        <img class="avatar-xl"
                                             src="{{ asset('uploads/logo/'.getSiteLogo()) }}" alt="logo"
                                             style="max-width: 150px ;max-height: 100px"/>
                                    </div>
                                </div>
                                <a href="{{ route('orders.index') }}">
                                    <button id="btnPrint" class="hidden-print btn btn-danger">Back</button>
                                </a>
                                <hr>
                                <div class="form-group col-12">
                                    <label for="InvoiceUrl">Order Url</label>
                                    <input type="url" id="InvoiceUrl" class="form-control w-100"
                                           readonly
                                           disabled
                                           value="{{ route('order_show',($order->code)) }}">
                                </div>
                                <hr>
                                <div class="row" style="margin-bottom: 25px;">
                                    <div class="col-6">
                                    </div>
                                    <div class="col-6">
                                        <h4 class="float-right font-size-16">Order Id
                                            #&nbsp;{{ $order->code }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <address>
                                            <strong>Order To:</strong><br>
                                            {{ $order->user->name }}<br>

                                            <br>
                                        </address>
                                        <address>
                                            <strong>Shipping Address:</strong><br>
                                            {{ getProviceName($order->addresses->province_id) }}
                                            - {{ getDistrictName($order->addresses->district_id) }}
                                            - {{ $order->addresses->street }}
                                        </address>
                                    </div>

                                    {{--                                <div class="col-6 text-right">--}}
                                    {{--                                    <address>--}}
                                    {{--                                        <strong>Quotation By:</strong><br>--}}
                                    {{--                                        {{ getOrganization( $data->organization_id)->name }}<br>--}}
                                    {{--                                        --}}{{--                                        {{ getAddress() }}<br>--}}
                                    {{--                                        --}}{{--                                        PAN/VAT No.:&nbsp;{{ getPanVat() }}<br>--}}
                                    {{--                                        --}}{{--                                        Email:&nbsp;{{ getEmail() }}<br>--}}
                                    {{--                                        --}}{{--                                        Contact No.:&nbsp;{{ getPhoneNumber() }}<br>--}}
                                    {{--                                    </address>--}}
                                    {{--                                </div>--}}
                                </div>

                                <div class="row">
                                    <div class="col-6 mt-3">
                                        <address>
                                            {{--                                        <strong>Subject:&nbsp;--}}
                                            {{--                                            {{ $data->subject }}--}}
                                            {{--                                        </strong><br><br>--}}
                                            <strong>Order
                                                Date:&nbsp;{{ date('Y-M-d',strtotime($order->created_at)) }}</strong><br>
                                            <strong>Delivery Estimated Date :&nbsp;{{ $order->date }}</strong>
                                        </address>
                                    </div>
                                </div>

                                <div class="py-2 mt-3">
                                    <h3 class="font-size-15 font-weight-bold">Order summary</h3>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-nowrap ">
                                        <thead>
                                        <tr>
                                            <th style="width: 70px;">S.No</th>
                                            <th>Particulars</th>
                                            <th>Attribute</th>
                                            <th>Price (Rs.)</th>
                                            <th>Quantity</th>
                                            <th>Total Amount (Rs.)</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tbody>
                                        @foreach($order->orderDetails as $detail)
                                            @php
                                                $totalPrice = 0;
                                                $shippingPrice = getShipingPrice($order->shipping_method_id);
                                                $totalPrice = $detail->price * $detail->quantity ;
                                            @endphp


                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>
                                                    {{ $detail->product->name }}
                                                </td>
                                                <td>{{ isset($detail->productAttribute->attribute->name) ? $detail->productAttribute->attribute->name .'('. $detail->productAttribute->value.')' : '' }}</td>
                                                <td>Rs.{{ $detail->price}}</td>
                                                <td>{{ $detail->quantity }}</td>
                                                <td>Rs.{{ $totalPrice }}</td>
                                            </tr>

                                        @endforeach
                                        </tbody>
                                        </tbody>
                                        <tbody>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Grand Total : Rs.{{ $order->grand_total }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Total Coupon Discount : Rs.{{ $order->total_coupon_discount }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Total Discount : Rs.{{ $order->discount_amount }}</td>
                                        </tr>

                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Total Tax Amount : Rs.{{ $order->tax_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Delivery Amount : Rs.{{ $order->delivery_vendor_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Shipping Amount : Rs.{{ $shippingPrice }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Net Amount : Rs.{{ $order->net_total + $shippingPrice}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="py-2 mt-3">
                                    @php
                                        $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                    @endphp
                                    <h4 class="font-size-15 font-weight-bold">Amount In words: Nepalese
                                        Rupees {{ $digit->format($order->net_total + $shippingPrice) }}</h4>
                                </div>
                                {{--                            <div class="row" style="margin-top: 30px;">--}}
                                {{--                                <p>Remarks: {{ $data->remarks }}</p>--}}
                                {{--                            </div>--}}
                                {{--                            <div class="row" style="margin-top: 30px;">--}}
                                {{--                                <p>Remarks: {{ $data->remarks }}</p>--}}
                                {{--                                @if($data->proposal)--}}
                                {{--                                    <a href="{{ asset('uploads'. $data->proposal) }}"--}}
                                {{--                                       download--}}
                                {{--                                       class="ml-2 btn btn-outline-dark">--}}
                                {{--                                        <i class="bx bx-download"></i> Download Proposal--}}
                                {{--                                    </a>--}}
                                {{--                                @endif--}}
                                {{--                            </div>--}}

                            </div>
                        </div>

                    </div>
                </div>
                <!-- end row -->

            </div> <!-- container-fluid -->
        </div>
    </div>
    <!-- END layout-wrapper -->

@endsection

@section('script')

    <script type="text/javascript">

        function countStatusRecords(status_name) {

            $.ajax({
                url: "{{ route('countTotalStatusRecords') }}",
                method: "GET",
                data: {
                    status_name
                },
                success: function (responseData) {
                    $('#myTab').html(responseData.result)
                }
            })
        }


        function countFilteredRecords(fiscal_year = null, status = 'Pending', start_date = null, end_date = null, year = null, month = year) {

            $.ajax({
                url: "{{ route('countFilteredRecords') }}",
                method: "GET",
                data: {
                    fiscal_year,
                    status,
                    start_date,
                    end_date,
                    year,
                    month
                },
                success: function (responseData) {
                    $('#myTab').html(responseData.result)
                }
            })
        }

        function viewCustomerProfile(order_id) {
            $.ajax({
                url: "{{ route('getCustomerProfile') }}",
                data: {
                    order_id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            });
        }

        function viewOrderLog(vendor_id, logistic_order_code, token, user_id) {
            $.ajax({
                url: "{{ route('view_logistic_vendor_order_log') }}",
                data: {
                    vendor_id,
                    logistic_order_code,
                    token,
                    user_id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    } else {
                        toastr.error(responseData.msg)
                    }
                }
            })
        }

        var PendingOrderModule = {};
        var OrderModule = {};
        let tgl = 1;

        function toggleFilter() {
            if (tgl == 1) {
                tgl = 0
                $("#basic_filter_field").html("")
                $("#basic_filter_field").append($("#advance").html())
                $("#FilterToggle").html(' <i class="fa fa-align-justify me-3"></i>Basic Filter')
            } else {
                tgl = 1
                $("#basic_filter_field").html("")
                $("#basic_filter_field").append($("#basic").html())
                $("#FilterToggle").html(' <i class="fa fa-align-justify me-3"></i>Advance Filter')
                $('#year').select2()
                $('#month').select2()
            }

        }

        $(document).on('click', '#myTab a', function (e) {
            $(this).tab('show');
            let fiscal_year = null
            fiscal_year = $('#FiscalYear').val()

            let val = null;
            val = $(this).text()

            let status = null;
            status = val.split('(')[0]

            let tab_start_date = null
            tab_start_date = $('#start').val()

            let tab_end_date = null;
            tab_end_date = $('#end').val()

            let tab_year = null
            tab_year = $('#year').val()

            let tab_month = null
            tab_month = $('#month').val()

            // countStatusRecords(status)
            countFilteredRecords(fiscal_year, status, tab_start_date, tab_end_date, tab_year, tab_month)
            datatable(fiscal_year, status, tab_start_date, tab_end_date, tab_year, tab_month)

        });

        function datatable(fiscal_year, status = 'Pending', start_date = null, end_date = null, year = null, month = null) {


            $('.pending').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{url('/getOrders/')}}" + '/' + 'pending',
                    type: "GET",
                    data: {
                        fiscal_year,
                        status,
                        start_date,
                        end_date,
                        year,
                        month
                    }
                },
                scrollY: "50vh",
                scrollX: true,
                lengthMenu: [
                    [10, 20, -1],
                    [10, 20, "All"]
                ],
                order: [
                    [1, "DESC"]
                ],
                columns: [
                    {
                        data: 'id',
                        name: 'orders.id'
                    },
                    {
                        data: 'fiscal_year_id',
                        name: 'orders.fiscal_year_id'
                    },
                    {
                        data: 'code',
                        name: 'orders.code'
                    },
                    {
                        data: 'name',
                        name: 'users.name'
                    },
                    {
                        data: 'contact',
                        name: 'users.contact'
                    },
                    // {
                    //     data: 'delivery_status',
                    //     name: 'orders.delivery_status'
                    // },
                    // {
                    //     data: 'shipping_address',
                    //     name: 'address.shipping_address'
                    // },
                    // {
                    //     data: 'net_total',
                    //     name: 'orders.net_total'
                    // },
                    // {
                    //     data: 'net_amount',
                    //     name: 'sales_profit_loss.net_amount'
                    // },
                    // {
                    //     data: 'mode_type',
                    //     name: 'orders.mode_type'
                    // },
                    // {
                    //     data: 'order_by',
                    //     name: 'orders.order_by'
                    // },
                    {
                        data: "action"
                    },
                ],
                "drawCallback": function (settings) {
                    const response = settings.json
                    $("#FilterPaymentCollection").html(response.total_amount)
                },
                columnDefs: [{
                    "orderable": false,
                    "targets": [5]
                }],
            });
        }

        function changeOrderStatus(id) {
            $.ajax({
                url: "{{ route('change_order_status') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            })
        }

        function paymentSettlement(id) {

            $.ajax({
                url: "{{ route('cancellation_payment_settlement') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            })
        }

        function paymentForSale(id) {
            $.ajax({
                url: "{{ route('sales_payments_track') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            })
        }

        function paymentForLogistic(id) {
            $.ajax({
                url: "{{ route('paymentForLogistic') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            })
        }

        $(document).ready(function () {

            $('#basic_filter_field').append($('#basic').html())
            $('#year').select2()
            $('#month').select2()

            $('#FiscalYear').select2()

            $('#search').on('click', function () {
                const fiscal_year = $('#FiscalYear').val()
                const start_date = $('#start').val()
                const end_date = $('#end').val()
                const year = $('#year').val()
                const month = $('#month').val()

                const nav = $('.nav-item .active').text()
                const status = nav.split('(')[0]
                countFilteredRecords(fiscal_year, status, start_date, end_date, year, month)
                datatable(fiscal_year, status, start_date, end_date, year, month)
            })

            datatable()
        });


        {{--function showOrders(status) {--}}
        {{--    OrderModule.status = $('.' + status).DataTable({--}}
        {{--        dom: 'lfrtip',--}}
        {{--        destroy: true,--}}
        {{--        processing: true,--}}
        {{--        language: {--}}
        {{--            processing: '<span style="color:white;">Processing...</span>'--}}
        {{--        },--}}
        {{--        serverSide: true,--}}
        {{--        ajax: {--}}
        {{--            url: "{{url('/getOrders/')}}" + '/' + status,--}}
        {{--            type: "GET",--}}
        {{--            data: function (postData) {--}}
        {{--            }--}}
        {{--        },--}}
        {{--        scrollY: "50vh",--}}
        {{--        scrollX: true,--}}
        {{--        lengthMenu: [--}}
        {{--            [10, 20, -1],--}}
        {{--            [10, 20, "All"]--}}
        {{--        ],--}}
        {{--        order: [--}}
        {{--            [1, "DESC"]--}}
        {{--        ],--}}
        {{--        columns: [{--}}
        {{--            data: 'id',--}}
        {{--            name: 'orders.id'--}}
        {{--        },--}}
        {{--            {--}}
        {{--                data: 'code',--}}
        {{--                name: 'orders.code'--}}
        {{--            },--}}
        {{--            {--}}
        {{--                data: 'name',--}}
        {{--                name: 'users.name'--}}
        {{--            },--}}
        {{--            {--}}
        {{--                data: 'email',--}}
        {{--                name: 'users.email'--}}
        {{--            },--}}
        {{--            {--}}
        {{--                data: 'billing_address',--}}
        {{--                name: 'address.billing_address'--}}
        {{--            },--}}
        {{--            {--}}
        {{--                data: 'shipping_address',--}}
        {{--                name: 'address.shipping_address'--}}
        {{--            },--}}


        {{--            {--}}
        {{--                data: "action"--}}
        {{--            },--}}
        {{--        ],--}}
        {{--        columnDefs: [{--}}
        {{--            "orderable": false,--}}
        {{--            "targets": [6]--}}
        {{--        }],--}}
        {{--    });--}}

        {{--}--}}

        function showOrderDetailModal(code) {
            $.get("{{ url('/order/detail/')}}" + '/' + code, function (data) {
                $('#OrderModal #OrderModalContent').html(data);
                $("#OrderModal").modal('show');
            });
        }

        function sendMail(id) {

            $.ajax({
                url: "{{ route('sendMail') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }
                }
            })
        }

        function printOrder(id) {

            $.ajax({
                url: "{{ route('print_order') }}",
                method: "GET",
                data: {
                    id
                },
                success: function (responseData) {
                    if (responseData.response) {
                        $('html').empty().html(responseData.result);
                        window.print()
                        location.reload()
                    }
                }
            })

        }
    </script>

    <script type="text/template" id="advance">
        <div class="input-daterange input-group" id="datepicker6"
             data-date-format="yyyy-mm-dd" data-date-autoclose="true"
             data-provide="datepicker" data-date-container='#datepicker6'>
            <input type="text" class="form-control" name="start" required
                   autocomplete="off" id="start"
                   placeholder="Start Date"
                   style="border-radius: 20px 0 0 20px;" value="{{ now()->toDateString() }}">
            <input type="text" class="form-control" name="end"
                   placeholder="End Date" id="end"
                   required autocomplete="off"
                   style="border-radius: 0 20px 20px 0;" value="{{ now()->toDateString() }}">
        </div>
    </script>
    <script type="text/template" id="basic">
        <div class="col-lg-5 col-md-6 col-sm-12 mt-2">
            <div class="mb-3 item">
                <select name="doctor_id" id="year" required class="form-control"
                        style="width: 100%;">
                    <option value="" selected disabled>Select Year
                    </option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                    <option value="2028">2028</option>
                    <option value="2029">2029</option>
                    <option value="2030">2030</option>
                    <option value="2031">2031</option>
                </select>
            </div>
        </div>
        <div class="col-lg-7 col-md-6 col-sm-12 mt-2">
            <div class="mb-3 item">
                <select name="doctor_id" id="month" required class="form-control"
                        style="width: 100%;">
                    <option value="" selected disabled>Select month
                    </option>
                    <option value="01">Jan</option>
                    <option value="02">Feb</option>
                    <option value="03">Mar</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">Aug</option>
                    <option value="09">Sept</option>
                    <option value="10">Oct</option>
                    <option value="11">Nov</option>
                    <option value="12">Dec</option>

                </select>
            </div>
        </div>
    </script>
@endsection
