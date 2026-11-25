<div class="modal-header">
    <h5 class="modal-title">Stock Details of {{ $product->name }}</h5>
    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="table-responsive">
        <table id="productStockDetailsTbl"
               class="table table-bordered dt-responsive nowrap w-100"
               style="width:100%">
            <thead class="table-light">
            <tr>
                <th class="align-middle">S. No.</th>
                <th class="align-middle">Date</th>
                <th class="align-middle">Variant</th>
                <th class="align-middle">QYT</th>
                <th class="align-middle">Ordered Quantity</th>
                <th class="align-middle">Remaining Quantity</th>
                <th class="align-middle">Price</th>
            </tr>
            </thead>
            <tbody>
            @php
                $sn = 1;
                $total_quantity = 0;
                $total_ordered_qty = 0;
                $total_remaining_qty =0;

             @endphp

            @foreach($records as $record)
                @php
                    $attribute = \App\Models\ProductAttribute::where('id', $record->attribute_id)->where('is_active', true)->first();
                    $data = '';
                    $ordered_quantity = [];
                    if($attribute){
                        $attribute_value = $attribute->value;
                        $attribute_name = $attribute->attribute->name;
                        $data = $attribute_name . ' [ ' . $attribute_value . ' ] ';
                    }

                    $ordered_quantity  = \App\Models\OrderDetail::where([
                        ['product_id',$record->product_id],
                        ['attribute',$record->attribute_id],
                        ['delivery_status',3]
                    ])->sum('quantity');

                    $remaining_quantity = $record->quantity - $ordered_quantity;
                    $total_quantity += $record->quantity;
                    $total_ordered_qty += $ordered_quantity;
                    $total_remaining_qty +=$remaining_quantity;
                @endphp
                <tr>
                    <td>{{ $sn ++ }}</td>
                    <td>{{ $record->created_at->format('Y M d') }}</td>
                    <td> {{ $data }}  </td>
                    <td>{{ $record->quantity }}</td>
                    <td>{{ $ordered_quantity }}</td>
                    <td>{{ $remaining_quantity}}</td>
                    <td>{{ $record->price }}</td>
                </tr>
            @endforeach
            </tbody>
            <tbody>
            <td colspan="3">
            </td>
            <td><b>Total Qty : {{ $total_quantity }}</b></td>
            <td><b>Total Ordered Quantity : {{ $total_ordered_qty }}</b></td>
            <td><b>Total Remaining Quantity : {{ $total_remaining_qty }}</b></td>
            </tbody>
        </table>
    </div>
    <!-- end table-responsive -->
</div>

