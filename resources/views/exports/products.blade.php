<table>
    <thead>
        <tr>
            <th>name</th>
            <th>slug</th>
            <th>brand_id</th>
            <th>category_id</th>
            <th>description</th>
            <th>sku</th>
            <th>purchased_price</th>
            <th>total</th>
            <th>ordered</th>
            <th>remaining</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        @php $total = 0;
        $ordered = 0; @endphp
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->slug }}</td>
            <td>{{ $product->brand_id }}</td>
            <td>{{ $product->category_id }}</td>
            <td>{{ $product->description }}</td>
            <td>{{ $product->sku }}</td>
            @foreach($product->productMasters as $master)
            @if($master->product_id == $product->id)
            @php $total = intVal($total) + intVal($master->quantity); @endphp
            @if($master->is_active == 1)
            <td>{{ $master->price }}</td>
            @endif
            @endif
            @endforeach
            <td>{{$total}}</td>

            @foreach($product->orderDetails as $detail)
            @if($detail->product_id == $product->id)
            @php $ordered = intVal($ordered) + intVal($detail->quantity); @endphp
            @endif

            @endforeach
            <td>{{$ordered}}</td>

            <td>{{$total - $ordered}}</td>
        </tr>
        @endforeach
    </tbody>
</table>