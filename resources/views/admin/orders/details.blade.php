<style>
    @media screen {
        #printSection {
            display: none;
        }
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printSection,
        #printSection * {
            visibility: visible;
        }

        #printSection {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>


<div id="printThis">
    <div class="modal-header">
        <h5 class="modal-title" id="productModalLabel">Order # {{ $order->code }}</h5>
    </div>

    <div class="modal-body">

        <div class="py-2 mt-3">
            <h3 class="font-size-15 fw-bold">Order summary</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-nowrap">
                <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Code</th>
                    <th>Item</th>
                    <th>Attribute</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Delivery status</th>
                    <th>Line Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->orderDetails as $details)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#{{ $details->code }}</td>
                        <td>{{ $details->product->name }}</td>
                        <td>
                            @if ($details->attribute != null)
                                @php $attribute = \App\Models\ProductAttribute::find($details->attribute); @endphp
                                [{{ $attribute->attribute->name }}
                                :&nbsp;{{ $attribute->value }}]
                            @else
                                no attributes
                            @endif

                        </td>
                        <td>Rs.{{ $details->price }}</td>
                        <td>{{ $details->quantity }}</td>
                        <td>
                            @php
                                $statusName = App\Models\OrderStatus::where('id', $details->delivery_status)->first();
                            @endphp

                            @if ($details->delivery_status == '3' || $details->delivery_status == '8')
                                {{ $statusName->name }}
                            @else
                                <select name="order_status" id="orderStatus"
                                        onchange="changeOrderStatus({{ $details->id }})">
                                    @foreach ($status as $s)
                                        <option value="{{ $s->id }}"
                                                @if ($s->id == $details->delivery_status) selected @endif>
                                            {{ ucfirst($s->name) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                        <td>Rs.{{ $details->price * $details->quantity }}</td>

                    </tr>
                @endforeach

                <tr>
                    <td colspan="5"></td>
                    <td colspan="2" class="text-end">Sub Total</td>
                    <td class="text-end" colspan="1">Rs.{{ $order->grand_total }}</td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2" class="border-0 text-end">
                        <strong>Shipping</strong>
                    </td>
                    @if ($order->shippingMethod)
                        <td colspan="1" class="border-0 text-end">Rs. {{ $order->shippingMethod->cost }}</td>
                    @else
                        <td class="border-0 text-end">0</td>
                    @endif
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="2" class="border-0 text-end">
                        <strong>Total</strong>
                    </td>
                    <td colspan="1" class="border-0 text-end">
                        <h4 class="m-0">Rs.{{ $order->grand_total + $order->shippingMethod->cost }}</h4>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="d-print-none">
            <div class="float-end">
                <a onclick="printOrderInAdmin()" class="btn btn-success waves-effect waves-light me-1"><i
                        class="fa fa-print"></i></a>

                <a class="btn btn-secondary" data-bs-dismiss="modal" style="float: right;">Back</a>

            </div>
        </div>
    </div>
</div>
