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
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-3 col-sm-12 mt-2">
                                            <button class="btn btn-outline-primary waves-effect waves-light"
                                                    id="FilterToggle"
                                                    onclick="toggleFilter()">
                                                <i class="fa fa-align-justify me-3"></i>Advance Filter
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <form id="filter_report" method="get"
                                                  action="{{ route('getDateReport') }}">
                                            <div class="row">
                                                <div class="col-lg-8">

                                                        <div class="row float-end">
                                                            <div class="col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="FilterType">Report View Type</label>
                                                                    <select name="type" id="FilterType"
                                                                            class="form-control">
                                                                        <option value="table">Table</option>
                                                                        <option value="payment">Chart</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <div class="row" id="basic_filter_field">
                                                                </div>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="col-lg-4 col-md-3 col-sm-12 mt-2">
                                                        <button type="submit" id="save"
                                                                class="btn btn-primary">
                                                            filter report
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>

                                        <div class="card-body" id="ReportSection">
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
        const UserModule = {};
        let tgl = 1
        $(function () {

            $("#ReportSection").html($("#TableViewHtml").html())
            populate_datatable()

            $('#basic_filter_field').append($('#basic').html())
            $('#year').select2()
            $('#month').select2()
            // filterData()

            $("#filter_report").submit(function (event) {

                event.preventDefault();
                var year = null;
                var month = null;
                var start_date = null;
                var end_date = null;
                var year = $('#year').val();
                var month = $('#month').val();
                var start_date = $('#start').val();
                var end_date = $('#end').val();

                const type = $("#FilterType").val()

                if (type === 'table') {
                    $("#ReportSection").html($("#TableViewHtml").html())
                    populate_datatable(year, month, start_date, end_date)
                } else {
                    paymentType(year, month, start_date, end_date)
                }
            })
        })

        function populate_datatable(year = null, month = null, start_date = null, end_date = null,type='income') {

            $('#datatable').DataTable({
                dom: 'lfrtip',
                destroy: true,
                processing: true,
                language: {
                    processing: '<span style="color:white;">Processing...</span>'
                },
                serverSide: true,
                ajax: {
                    url: "{{ route('getOrderStatusData') }}",
                    type: "GET",
                    data: {
                        start_date,
                        end_date,
                        year,
                        month,
                        type
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
                        data: 'net_total',
                        name: 'orders.net_total'
                    },
                    {
                        data: 'delivery_status',
                        name: 'orders.delivery_status'
                    },
                    {
                        data: 'created_at',
                        name: 'orders.created_at'
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

        function paymentType(year = null, month = null, start_date = null, end_date = null) {
            let labels, data, colors
            $.ajax({
                url: '{{ route('statusReport') }}',
                type: 'GET',
                data: {
                    year,
                    month,
                    start_date,
                    end_date
                },
                success: function (response) {
                    if (response.response) {

                        const responseDate = response.result
                        labels = responseDate.labels
                        colors = responseDate.colors
                        data = responseDate.data

                        $("#ReportSection").html('<canvas id="PaymentMode" height="500"></canvas>')

                        !function (l) {
                            "use strict";

                            function r() {
                            }

                            r.prototype.respChart = function (r, o, e, a) {
                                Chart.defaults.global.defaultFontColor = "#8791af", Chart.defaults.scale.gridLines.color = "rgba(166, 176, 207, 0.1)";
                                const t = r.get(0).getContext("2d"), n = l(r).parent();

                                function i() {
                                    r.attr("width", l(n).width());
                                    new Chart(t, {type: "pie", data: e, options: a});
                                }

                                l(window).resize(i), i()
                            }, r.prototype.init = function () {
                                this.respChart(l("#PaymentMode"), "Pie", {
                                    labels: labels,
                                    datasets: [{
                                        data: data,
                                        backgroundColor: colors,
                                        hoverBackgroundColor: colors,
                                        hoverBorderColor: "#fff"
                                    }]
                                });
                            }, l.ChartJs = new r, l.ChartJs.Constructor = r
                        }(window.jQuery), function () {
                            "use strict";
                            window.jQuery.ChartJs.init()
                        }();

                    } else {
                        toastr.error(response.msg)
                        $("#ReportSection").html($("#TableViewHtml").html())
                        populate_datatable()
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

    <template id="TableViewHtml">
        <div class="table-responsive">
            <table id="datatable"
                   class="table table-bordered dt-responsive nowrap"
                   width="100%">
                <thead class="table-light">
                <tr style="  text-transform: uppercase;">
                    <th class="align-middle">S.N.</th>
                    <th class="align-middle">Fiscal Year</th>
                    <th class="align-middle">Code</th>
                    <th class="align-middle">Price</th>
                    <th class="align-middle">Payment Mode</th>
                    <th class="align-middle">Payment Date</th>
                </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
            <div>
                Total Amount :Rs. <span id="FilterPaymentCollection">0.00</span>
            </div>
        </div>
    </template>

@endsection

