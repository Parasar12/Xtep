<div class="modal-header">
    <h5 class="modal-title">Product Details</h5>
</div>

<div class="modal-body">
    <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
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
                                    <th scope="col">Profit Margin</th>
                                    <th scope="col">Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $grand_total=0;
                                    $productMasters = \App\Models\ProductMaster::where([
                                        ['product_id',$product->id],
                                        ['is_active',true]
])->get();
                                @endphp
                                @foreach($productMasters as $detail)
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
                                        <td>{{ $detail->quantity  }}</td>
                                        <td>Rs.{{ $detail->profit_margin  }}</td>
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
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-danger ml-3"
                                        data-bs-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
</div>
