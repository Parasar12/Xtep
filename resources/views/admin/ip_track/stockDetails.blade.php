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
                <th class="align-middle">Price</th>
            </tr>
            </thead>
            <tbody>
            @php $sn = 1; @endphp
            @foreach($records as $record)
                @php
                    $attribute = \App\Models\ProductAttribute::where('id', $record->attribute_id)->where('is_active', true)->first();
                    $data = '';
                    if($attribute){
                        $attribute_value = $attribute->value;
                        $attribute_name = $attribute->attribute->name;
                        $data = $attribute_name . ' [ ' . $attribute_value . ' ] ';
                    }

                @endphp
                <tr>
                    <td>{{ $sn ++ }}</td>
                    <td>{{ $record->created_at->format('Y M d') }}</td>
                    <td> {{ $data }}  </td>
                    <td>{{ $record->quantity }}</td>
                    <td>{{ $record->price }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- end table-responsive -->
</div>

