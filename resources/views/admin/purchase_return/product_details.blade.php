<div class="row">
    <h4 style="background-color: #bdc4cb">Product Name: {{ $product->name }}</h4>
    <h4 class="justify-content-end mx-5">Sku: {{ $product->sku }}</h4>
</div>
<input type="hidden" name="product_id" value="{{ $product->id }}">
<div class="row">
    <table class="table mt-5">
        <thead class="thead-dark">
        <tr>
            <th scope="col">S.N.</th>
            <th scope="col">Attribute Name</th>
            <th scope="col">Attribute Value</th>
            <th scope="col">Cost Price</th>
            <th scope="col">Selling  Price</th>
            <th scope="col">Quantity</th>
            <th scope="col">Total Price</th>
        </tr>
        </thead>
        <tbody>
        @php
            $grand_total=0;
        @endphp
        @foreach($details as $detail)
            @php
                $total = $detail->quantity * $detail->price;
                $grand_total +=$total;
            @endphp
            <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $detail->product_attribute->attribute->name ?? '' }}</td>
                <td>{{ $detail->product_attribute->value ?? '' }}</td>
                <td>{{ $detail->cost_price ?? 0 }}</td>
                <td>Rs.{{ $detail->price  }}</td>
                <td>Rs.{{ $detail->quantity  }}</td>
                <td>Rs.{{ $total}}</td>
            </tr>
        @endforeach
        </tbody>
        <tbody>
          <tr>
              <td colspan="5"></td>
              <td>Grand Total : Rs.{{ $grand_total }}</td>
          </tr>
        </tbody>
    </table>

</div>
