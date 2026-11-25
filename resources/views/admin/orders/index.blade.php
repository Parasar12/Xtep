@extends('layouts.app')

@section('content')

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include('admin.includes.header')

        @include('admin.includes.left_sidebar')
        <style>
            .nav-item {
                width: 20%;
            }
        </style>
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <div class="tab-content p-3 text-muted">
                        <div class="tab-pane active" id="pending" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-12 mt-2">
                                                    <button class="btn btn-outline-primary waves-effect waves-light"
                                                            id="FilterToggle"
                                                            onclick="toggleFilter()">
                                                        <i class="fa fa-align-justify me-3"></i>Advance Filter
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-3 col-md-6 col-sm-12">
                                                    <div class="form-group item">
                                                        <label for="FiscalYear" class="required-label">Fiscal
                                                            Year</label>
                                                        <select name="fiscal_year" id="FiscalYear" required
                                                                class="form-control">
                                                            <option value="" selected disabled>Select</option>
                                                            @foreach($fiscalYear as $year)
                                                                <option
                                                                    value="{{ $year->id }}">{{ $year->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="col-lg-5 mt-3">
                                                    <div class="row" id="basic_filter_field">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mt-3">
                                                    <button type="button" id="search" class="btn btn-outline-success">
                                                        Search
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="float-right">
                                                <a href="{{ route('sales_entry.add') }}"
                                                   class="btn btn-success btn-rounded waves-effect waves-light mb-2 me-2">
                                                    Add Order
                                                </a>
                                            </div>
                                            <h4 class="card-title mb-4">Orders</h4>

                                            <ul class="nav nav-tabs" id="myTab">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-toggle="tab"
                                                       id="Pending"
                                                       href="#pending">Pending({{ getTotalPending() }}
                                                        )</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link " id="Processing" data-toggle="tab" href="#processing">Processing({{ getTotalProcessing() }}
                                                        )</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="Delivered" data-toggle="tab"
                                                       href="#delivered">Delivered({{ getTotalDelivered() }})</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="Returned" data-toggle="tab"
                                                       href="#returned">Returned({{ getTotalReturned() }})</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="Cancelled" data-toggle="tab"
                                                       href="#cancelled">Cancelled({{ getTotalCancelled() }})</a>
                                                </li>
                                            </ul>

                                            <div class="table-responsive mt-5">
                                                <table
                                                    class="table table-bordered dt-responsive  nowrap w-100 show_data pending"
                                                    style="width:100%">
                                                    <thead class="table-light">
                                                    <tr>
                                                        <th class="align-middle">S. No.</th>
                                                        <th class="align-middle">Fiscal Year</th>
                                                        <th class="align-middle">Code</th>
                                                        <th class="align-middle">Customer Name</th>
                                                        <th class="align-middle">Customer Contact</th>
{{--                                                        <th class="align-middle">Order Status</th>--}}
{{--                                                        <th class="align-middle">Shipping Address</th>--}}
{{--                                                        <th class="align-middle">Net Total</th>--}}
{{--                                                        <th class="align-middle">Profit/Loss</th>--}}
{{--                                                        <th class="align-middle">Mode</th>--}}
{{--                                                        <th class="align-middle">Order By</th>--}}
                                                        <th class="align-middle order_action">Action</th>
                                                    </tr>
                                                    </thead>

                                                </table>
                                                <div>
                                                    <h4> Total Amount :<span id="FilterPaymentCollection">0.00</span>
                                                    </h4>
                                                </div>
                                            </div>
                                            <!-- end table-responsive -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- END layout-wrapper -->

    <!-- Order Details Modal-->
    <div class="modal fade" tabindex="-1" id="OrderModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="OrderModalContent"></div>
        </div>
    </div>
    <!-- End of PageContent -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
@endsection

@section('script')

    <script type="text/javascript">

        function countStatusRecords(status_name){

            $.ajax({
                url:"{{ route('countTotalStatusRecords') }}",
                method:"GET",
                data:{
                    status_name
                },
                success:function (responseData){
                    $('#myTab').html(responseData.result)
                }
            })
        }


        function countFilteredRecords(fiscal_year = null, status = 'Pending', start_date = null, end_date = null, year = null, month = year){

            $.ajax({
                url:"{{ route('countFilteredRecords') }}",
                method:"GET",
                data:{
                    fiscal_year,
                    status,
                    start_date,
                    end_date,
                    year,
                    month
                },
                success:function (responseData){
                    $('#myTab').html(responseData.result)
                }
            })
        }

        function viewCustomerProfile(order_id){
           $.ajax({
               url:"{{ route('getCustomerProfile') }}",
               data:{
                   order_id
               },
               success:function (responseData){
                   if(responseData.response){
                       $('#OrderModal #OrderModalContent').html(responseData.result);
                       $("#OrderModal").modal('show');
                   }
               }
           });
        }

        function viewOrderLog(vendor_id,logistic_order_code,token,user_id){
            $.ajax({
                url:"{{ route('view_logistic_vendor_order_log') }}",
                data:{
                    vendor_id,
                    logistic_order_code,
                    token,
                    user_id
                },
                success:function (responseData){
                    if(responseData.response){
                        $('#OrderModal #OrderModalContent').html(responseData.result);
                        $("#OrderModal").modal('show');
                    }else{
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

        $(document).on('click','#myTab a',function (e) {
            $(this).tab('show');
            let fiscal_year = null
            fiscal_year = $('#FiscalYear').val()

            let val = null;
            val=$(this).text()

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

        function datatable(fiscal_year, status = 'Pending', start_date=null, end_date=null, year=null, month=null) {


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
