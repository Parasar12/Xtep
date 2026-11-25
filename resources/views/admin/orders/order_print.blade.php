<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print - {{ getSiteTitle() }}</title>
    <style>
        * {
            font-size: 18px;
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
            /*margin-left: 30px;*/
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


</head>
<body>
<div class="ticket">
    <p>
       <img src="{{ asset('uploads/logo/'.getSiteLogo()) }}" style="max-height: 40px">
    </p>

    <p>Purchase Order No : <b>#{{ $order->code }}</b></p>
    <p style="font-size: 10px;">Order Date : <b>{{ date('Y-M-d',strtotime($order->created_at)) }}</b></p>
    <p style="font-size: 10px;">Estimated Delivery Date # : <b>{{($order->date) }}</b></p>

    <p>Customer Name :<b>{{ $order->user->name }}</b> </p>
    <p>Contact Number :<b> {{ $order->user->contact }}</b></p>
    <p>Secondary Contact Number :<b>{{ $order->user->secondary_contact ?? '' }}</b> </p>
    <p>Customer Address : <b> {{ $address }}</b></p>
    <table class="mt-4">
        <thead>
        <tr>

            <th class="description">Product</th>
            <th class="quantity">Q.</th>
            <th class="price">Price</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->orderDetails as $detail)
            <tr>
                <td class="description">{{ $detail->product->name  }}{{  isset($detail->productAttribute->attribute->name) ? $detail->productAttribute->attribute->name .'('. $detail->productAttribute->value.')' : '' }} </td>
                <td class="quantity">{{ $detail->quantity }}</td>
                <td class="price">Rs. {{ intVal( $detail->price) }}</td>
            </tr>
        @endforeach
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
            <td colspan="2">Grand Total</td>
            <td>{{ $order->net_total  }}</td>
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

</div>

</body>
</html>
