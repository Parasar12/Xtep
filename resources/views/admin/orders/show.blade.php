{{--<div class="modal-header bg-soft-info p-3">--}}
{{--    <h5 class="modal-title" id="exampleModalLabel"></h5>--}}

{{--</div>--}}
{{--    <div class="modal-body" >--}}
{{--        <input type="hidden" id="id-field" />--}}
{{--        <div class="row g-3">--}}
{{--            <div class="col-lg-12">--}}
{{--                <div class="text-center">--}}
{{--                    <div class="position-relative d-inline-block">--}}
{{--                        <div class="position-absolute bottom-0 end-0">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--            <div class="col-lg-4">--}}
{{--                <div>--}}
{{--                    <label for="companyname-field" class="form-label">Customer Name : {{ $order->user->name }}</label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-4">--}}
{{--                <div>--}}
{{--                    <label for="owner-field" class="form-label">Customer Email: {{ $order->user->email }}</label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="col-lg-4">--}}
{{--                <div>--}}
{{--                    <label for="location-field" class="form-label">Customer Contact Number: {{ $order->user->contact }}</label>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-6">--}}
{{--                <div class="item">--}}
{{--                    <label for="location-field" class="form-label">Customer Secondary Contact Number: {{ $order->user->secondary_contact }}</label>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="col-lg-6">--}}
{{--                <div class="item">--}}
{{--                    <label for="location-field" class="form-label">Order Status: <span class="text-danger">{{ getOrderStatus($order->delivery_status) }}</span></label>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        @if($order)--}}
{{--            <div class="row mt-3">--}}
{{--                <table class="table">--}}
{{--                    <thead class="thead-dark">--}}
{{--                    <tr>--}}
{{--                        <th scope="col">S.N.</th>--}}
{{--                        <th scope="col">Product Name</th>--}}
{{--                        <th scope="col">Attribute</th>--}}
{{--                        <th scope="col">Price</th>--}}
{{--                        <th scope="col">Quantity</th>--}}
{{--                        <th scope="col">Total Price</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach($order->orderDetails as $detail)--}}
{{--                        @php--}}
{{--                            $totalPrice = 0;--}}
{{--                            $totalPrice = $detail->price * $detail->quantity;--}}
{{--                        @endphp--}}
{{--                        <tr>--}}
{{--                            <th scope="row">{{ $loop->iteration }}</th>--}}
{{--                            <td>--}}
{{--                                {{ $detail->product->name }}--}}
{{--                            </td>--}}
{{--                            <td>{{ isset($detail->productAttribute->attribute->name) ? $detail->productAttribute->attribute->name .'('. $detail->productAttribute->value.')' : '' }}</td>--}}
{{--                            <td>Rs.{{ $detail->price}}</td>--}}
{{--                            <td>{{ $detail->quantity }}</td>--}}
{{--                            <td>Rs.{{ $totalPrice }}</td>--}}
{{--                        </tr>--}}

{{--                    @endforeach--}}

{{--                    </tbody>--}}
{{--                    <tbody>--}}
{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Grand Total : Rs.{{ $order->grand_total }}</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Total Coupon Discount : Rs.{{ $order->total_coupon_discount }}</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Total  Discount : Rs.{{ $order->discount_amount }}</td>--}}
{{--                    </tr>--}}

{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Total  Tax Amount : Rs.{{ $order->tax_amount }}</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Delivery  Amount : Rs.{{ $order->delivery_vendor_amount }}</td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td colspan="5"></td>--}}
{{--                        <td>Net  Amount : Rs.{{ $order->net_total }}</td>--}}
{{--                    </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}

{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    <div class="modal-footer">--}}
{{--        <div class="hstack gap-2 justify-content-end">--}}
{{--            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>--}}
{{--            <!-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> -->--}}
{{--        </div>--}}
{{--    </div>--}}

    <!doctype html>
<html lang="en">
<head>


    <meta charset="utf-8"/>
    <title>Order | {{ getSiteTitle() }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    @php $siteFavicon = getSiteFavicon(); @endphp
    <link rel="icon" type="{{ $siteFavicon && strtolower(pathinfo($siteFavicon, PATHINFO_EXTENSION)) === 'png' ? 'image/png' : 'image/x-icon' }}" href="{{ $siteFavicon ? asset('uploads/favicon/' . $siteFavicon) : asset('favicon.ico') }}">

    <!-- Bootstrap Css -->
    <!-- jsvectormap css -->

    <script src="{{ asset('dashboard/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/validator.js') }}"></script>
    <script src="{{ asset('dashboard/app/app.js') }}"></script>

    <link href="{{ asset('dashboard/css/layout.css') }}" rel="stylesheet" type="text/css"/>


    <!-- Bootstrap Css -->
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css"/>


    <!-- Tree View  -->
    <link href="{{ asset('dashboard/plugins/jstree/style.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('dashboard/plugins/jstree/jstree.min.js') }}"></script>

    <!-- Toast -->
    <link href="{{ asset('dashboard/plugins/toast/build/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('dashboard/plugins/toast/build/toastr.min.js') }}"></script>

    <!-- Image Uploader -->
    <link href="{{ asset('dashboard/drag-drop-image-uploader/dist/image-uploader.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <script src="{{ asset('dashboard/drag-drop-image-uploader/dist/image-uploader.min.js') }}"></script>

    <link href="{{ asset('dashboard/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
          type="text/css">
    <style>
        @media print {
            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }

        .bhuwan_table_border {
            border: none !important;
        }
    </style>
</head>

<body>

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
                            <button id="btnPrint" class="hidden-print btn btn-primary">Print</button>
                            <hr>

                            <div class="row" style="margin-bottom: 25px;">
                                <div class="col-6">
                                </div>
                                <div class="col-6">
                                    <h4 class="float-right font-size-16">Order
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
                                        <strong>Order Date:&nbsp;{{ date('Y-M-d',strtotime($order->date)) }}</strong>
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
                                            $totalPrice = $detail->price * $detail->quantity;
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
                                        <td>Total  Discount : Rs.{{ $order->discount_amount }}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Total  Tax Amount : Rs.{{ $order->tax_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Delivery  Amount : Rs.{{ $order->delivery_vendor_amount }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td>Net  Amount : Rs.{{ $order->net_total }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="py-2 mt-3">
                                @php
                                    $digit = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                                @endphp
                                <h4 class="font-size-15 font-weight-bold">Amount In words: Nepalese
                                    Rupees {{ $digit->format($order->net_total) }}</h4>
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
    <!-- End Page-content -->
</div>
<!-- END layout-wrapper -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script>
    $(function(){
        const $btnPrint = document.querySelector("#btnPrint");
        $btnPrint.addEventListener("click", () => {
            window.print();
        });
    })
</script>
</body>
</html>

