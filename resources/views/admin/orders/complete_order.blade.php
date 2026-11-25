<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print - {{ getSiteTitle() }}</title>
    <style>
        * {
            font-size: 12px;
            font-family: 'Times New Roman';
        }

        td,
        th,
        tr,
        table {
            border-top: 1px solid black;
            border-collapse: collapse;
        }

        td.description,
        th.description {
            width: 75px;
            max-width: 75px;
        }

        td.quantity,
        th.quantity {
            width: 10px;
            max-width: 15px;
            word-break: break-all;
        }

        td.price,
        th.price {
            width: 80px;
            max-width: 80px;
            word-break: break-all;
        }

        .centered {
            text-align: center;
            align-content: center;
        }

        .ticket {
            width: 235px;
            max-width: 235px;
            margin-left: 100px;
        }

        img {
            max-width: inherit;
            width: inherit;
        }

        @media print {
            .hidden-print,
            .hidden-print * {
                display: none !important;
            }
        }
    </style>

    <!-- Bootstrap Css -->
    <link href="{{ asset('dashboard/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('dashboard/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css"/>

    <link href="{{ asset('dashboard/plugins/toast/build/toastr.min.css') }}" rel="stylesheet" type="text/css"/>

</head>
<body>
<div class="ticket">
    <p>
        <img src="{{ asset('uploads/logo/'.getSiteLogo()) }}" style="max-height: 40px">
    </p>
    @guest
        <button class="btn btn-success" onclick="completeOrder()"><h5 style="color: white">Complete Order</h5></button>
    @endguest

    <p>Purchase Order No : <b>#{{ $order->code }}</b></p>
    <p style="font-size: 10px;">Order Date : <b>{{ date('Y-M-d',strtotime($order->created_at)) }}</b></p>
    <p style="font-size: 10px;">Estimated Delivery Date # : <b>{{($order->date) }}</b></p>

    <p>Customer Name :<b>{{ $order->user->name }}</b></p>
    <p>Contact Number :<b> {{ $order->user->contact }}</b></p>
    <p>Secondary Contact Number :<b>{{ $order->user->secondary_contact ?? '' }}</b></p>
    <p>Customer Address : <b> {{ $address }}</b></p>
    <table class="mt-4">
        <thead>
        <tr>

            <th class="description">Product</th>
            <th class="description">Variant</th>
            <th class="quantity">Q.</th>
            <th class="price">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->orderDetails as $detail)
            <tr>
                <td class="description">{{ $detail->product->name  }} </td>
                <td class="description"> {{  isset($detail->productAttribute->attribute->name) ? $detail->productAttribute->attribute->name .'('. $detail->productAttribute->value.')' : '' }} </td>
                <td class="quantity">{{ $detail->quantity }}</td>
                <td class="price">Rs. {{ intVal( $detail->price) }}</td>
            </tr>
        @endforeach
        @php
            $totalPrice = 0;
            $shippingPrice = getShipingPrice($order->shipping_method_id);
            $totalPrice = $detail->price * $detail->quantity ;
        @endphp
        <tr>
            <td colspan="2">Sub Total</td>
            <td>{{ $order->grand_total }}</td>
        </tr>

        <tr>
            <td colspan="2">Delivery Charge</td>
            <td>{{ $order->delivery_vendor_amount }}</td>
        </tr>
        <tr>
            <td colspan="2">Discount Amount</td>
            <td>{{ $order->discount_amount }}</td>
        </tr>
        <tr>
            <td colspan="2">Shipping Total</td>
            <td>{{ $shippingPrice }}</td>
        </tr>
        <tr>
            <td colspan="2">Grand Total</td>
            <td>{{ $order->net_total +$shippingPrice }}</td>
        </tr>

        <tr>
            <td colspan="2">Paid Amount</td>
            <td>{{ $total_paid_amount }}</td>
        </tr>

        <tr>
            <td colspan="2">Due Amount</td>
            <td>{{ $due_amount }}</td>
        </tr>

        </tbody>
    </table>
    <br>
    @if($order->customer_remarks)
        <hr>
        <p>Customer Remarks : {{ $order->customer_remarks }}</p>
    @endif
    <hr>
    <p>{!! getBillFooterText() !!}</p>
    <div class="modal fade" tabindex="-1" id="OrderModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" id="OrderModalContent"></div>
        </div>
    </div>
</div>
<script src="{{ asset('dashboard/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('dashboard/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('dashboard/libs/node-waves/waves.min.js') }}"></script>

<script src="{{ asset('dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dashboard/js/validator.js') }}"></script>
<script src="{{ asset('dashboard/app/app.js') }}"></script>
<script src="{{ asset('dashboard/js/app.js') }}"></script>
<!-- Toast -->
<script src="{{ asset('dashboard/plugins/toast/build/toastr.min.js') }}"></script>

<script type="text/javascript">
    $(function () {
        init_validator();
        validator.defaults.alerts = false;
    })

    function completeOrder() {
        $('#OrderModal #OrderModalContent').html($('#complete_order_template').html());
        $("#OrderModal").modal('show');
    }
</script>
<template id="complete_order_template">
    <div class="modal-header">
        <h5 class="modal-title">Complete Payment</h5>
    </div>

    <div class="modal-body">
        <form method="post" action="{{route('complete_order_from_url')}}" class="eq-section" novalidate
              id="customer_address_post">@csrf
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3 row">
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <div class="col-md-6 item">
                                    <label for="address" class="col-form-label required-label">MPIN</label>
                                    <input type="number" required id="address" name="mpin" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3 row">

                                <div class="col-md-6 item">
                                    <label for="payment_method" class="col-form-label required-label">Payment
                                        Method</label>
                                    <select id="payment_method" class="form-control" required name="payment_method_id">
                                        <option value="">select payment method</option>
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}"> {{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">

                                <div class="col-md-6 item">
                                    <label for="address" class="col-form-label required-label">Due Amount</label>
                                    <input type="text" required id="address" value="{{ $due_amount }}" name="due_amount"
                                           class="form-control" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row">

                                <div class="col-md-6 item">
                                    <label for="address" class="col-form-label required-label">Received
                                        Amount(Rs.)</label>
                                    <input type="text" required id="address" name="received_amount"
                                           class="form-control">
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
</body>
</html>
