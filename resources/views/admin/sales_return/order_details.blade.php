<div class="row">
    <h4 style="background-color: #bdc4cb">Customer Name: {{ $order->user->name }}</h4>
    <h4 class="justify-content-end mx-5">Order Code: {{ $order->code }}</h4>
</div>
<input type="hidden" name="order_id" value="{{ $order->id }}">
<div class="row">
    <table class="table mt-5">
        <thead class="thead-dark">
        <tr>
            <th scope="col">S.N.</th>
            <th scope="col">Product</th>
            <th scope="col">Price </th>
            <th scope="col">Quantity</th>
            <th scope="col"> Coupon Discount</th>
            <th scope="col">Total Price</th>
        </tr>
        </thead>
        <tbody>

        @foreach($details as $detail)
            @php
                $total_price = ($detail->price * $detail->quantity) - $detail->product_coupon_discount;
            @endphp
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $detail->product->name ?? '' }}</td>
                <td>Rs.{{ $detail->price  }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>Rs.{{ $detail->product_coupon_discount  }}</td>
                <td>Rs.{{$total_price  }}</td>
            </tr>
        @endforeach
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
              <td>Vendor  Amount : Rs.{{ $order->delivery_vendor_amount }}</td>
          </tr>
{{--          <tr>--}}
{{--              <td colspan="5"></td>--}}
{{--              <td>Shipping Amount : Rs.{{ getShipingPrice($order->shipping_method_id) }}</td>--}}
{{--          </tr>--}}
          <tr>
              <td colspan="5"></td>
              <td>Net  Amount : Rs.{{ $order->net_total }}</td>
          </tr>
        </tbody>
    </table>

</div>
