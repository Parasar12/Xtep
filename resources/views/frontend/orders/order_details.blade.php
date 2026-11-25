<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-danger btn-rounded waves-effect waves-light" onclick="backToDateWiseOrders()"><i class="fa fa-arrow-left"> back</i></button>
</div>
<div class="table-responsive">
    <table class="table ">
        <thead >
            <tr>
                <th class="product-remove">&nbsp;</th>
                <th class="product-thumbnail">Image</th>
                <th class="product-name">Product Info</th>
                <th class="product-price">Quantity</th>
                <th class="product-name">Order Status</th>
            </tr>
        </thead>
        <tbody>
        @if($dateOrders->count())
            @foreach ($dateOrders as $order)
                @foreach($order->orderDetails as $detail)
                    <tr>
                        <!-- Status -->
                        <td class="wishlist-product-thumbnail text-center align-middle">
                            @if ($detail->delivery_status == 8)
                                <span class="badge bg-warning text-white p-2">Cancelled</span>
                            @else
                                @if($detail->delivery_status == 1)
                                    <button type="button" class="btn btn-danger btn-sm"
                                            id="cancelBtn-{{ $detail->id }}"
                                            onclick="changeOrderStatus({{$detail->id }},'{{ date('Y-m-d',strtotime($order->created_at)) }}')">
                                        X
                                    </button>
                                @endif
                            @endif
                        </td>

                        <!-- Product Image -->
                        <td class="wishlist-product-img text-center align-middle">
                            <a href="{{ route('front.product.details', $detail->product->slug) }}">
                                <img src="{{ asset('uploads/products/' . $detail->product->image) }}" 
                                     alt="{{ $detail->product->name }}" 
                                     class="img-fluid img-thumbnail" 
                                     style="max-width: 100px;">
                            </a>
                        </td>

                        <!-- Product Info -->
                        <td class="wishlist-product-info align-middle">
                            <h5 class="mb-2">
                                <a href="{{ route('front.product.details', $detail->product->slug) }}" class="text-dark">
                                    {{ $detail->product->name }}
                                </a>
                                @if ($detail->attribute != null)
                                    @php $attribute = \App\Models\ProductAttribute::find($detail->attribute); @endphp
                                    @if($attribute)
                                        <span class="text-muted d-block">
                                            [{{ $attribute->attribute->name }}: {{ $attribute->value }}]
                                        </span>
                                    @endif
                                @endif
                            </h5>
                            <div class="wishlist-product-price">
                                @if($detail->product_coupon_discount > 0)
                                    @php
                                        $price = $detail->price - $detail->product_coupon_discount;
                                        $total = $price * $detail->quantity;
                                    @endphp
                                    <span class="text-success fw-bold">Rs.{{ $price }}</span><br>
                                    <span class="text-muted">Coupon Discount: Rs.{{ $detail->product_coupon_discount }}</span>
                                @else
                                    <span class="text-success fw-bold">Rs.{{ $detail->price * $detail->quantity }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $detail->created_at->format('M d, Y') }}</small>
                        </td>

                        <!-- Quantity -->
                        <td class="wishlist-product-add-wrap text-center align-middle">
                            <h5 class="mb-0">QTY {{ $detail->quantity }}</h5>
                        </td>

                        <!-- Order Status -->
                        <td class="wishlist-product-add-wrap text-center align-middle">
                            <h5 class="mb-0">{{ getOrderStatus($detail->delivery_status) }}</h5>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">No Orders Found</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>






<!-- Corrected Modal HTML -->
<div class="modal" id="addBannerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="addBannerModalContent">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Cancellation Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="login-register-wrap login-register-gray-bg" style="width: 100%">
                    <div class="login-register-title">
                        <h1>Reason For Cancellation</h1>
                    </div>
                    <div class="login-register-form">
                        <form action="{{ route('order.cancel') }}" method="post" autocomplete="off" id="orderCancellation" novalidate class="eq-section">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 item">
                                    <div class="login-register-input-style input-style input-style-white">
                                        <label class="required-label">Reason For Cancellation</label>
                                        <textarea class="form-control" name="reason" required cols="12"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for order ID and order date -->
                            <input type="hidden" name="id" id="order_id">
                            <input type="hidden" name="order_date" id="order_date">

                            <div class="d-flex flex-row justify-content-between mt-2">
                                <div class="">
                                    <button type="submit" class="btn btn-success btn-rounded " onclick="closeModal()">Submit</button>
                                </div>
                                <div class="">
                                    <button type="button" class="btn btn-danger btn-rounded " data-bs-dismiss="modal" onclick="closeModal()">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>