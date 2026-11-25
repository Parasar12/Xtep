@if (Auth::check())
    @if ($cartList->count() > 0)
        @php
            $total = 0;
        @endphp
        <ul>
            @foreach ($cartList as $item)
                @php
                    $product = App\Models\Product::where('id', $item->product_id)->first();
                    $total += getProductPrice($item->product_id) * $item->quantity;
                @endphp
                <li>
                    <div class="shopping-cart-img">
                        <a href="{{ route('front.product.details', $product->slug) }}"><img alt="{{ $product->name }}"
                                                                                            src="{{ asset('uploads/products/' . $product->image) }}"></a>
                    </div>
                    <div class="shopping-cart-title">
                        <h4><a href="{{ route('front.product.details', $product->slug) }}">{{ $product->name }}</a></h4>
                        <h3><span>{{ $item->quantity }} × </span>{{ getProductPrice($item->product_id) }}</h3>
                    </div>
                    <div class="shopping-cart-delete">
                        <a href="#"><i class="far fa-times"></i></a>
                    </div>
                </li>
            @endforeach

        </ul>
        <div class="shopping-cart-footer">
            <div class="shopping-cart-total">
                <h4>Total <span>Rs.{{ $total }}</span></h4>
            </div>
            <div class="shopping-cart-button">
                <a href="{{ route('cart.list') }}">View cart</a>
                <a href="{{ route('cart.shipping') }}">Checkout</a>
            </div>
        </div>
    @else
        <h5> No Cart Items </h5>
    @endif
@endif

@if (Session::has('cart'))
    @if ($cartList)
        @php
            $total = 0;
        @endphp
        <ul>
            @foreach ($cartList as $item)
                @php
                    $product = App\Models\Product::where('id', $item->product_id)->first();
                    $total += getProductPrice($item->product_id) * $item->quantity;
                @endphp
                <li>
                    <div class="shopping-cart-img">
                        <a href="{{ route('front.product.details', $product->slug) }}"><img alt="{{ $product->name }}"
                                                                                            src="{{ asset('uploads/products/' . $product->image) }}"></a>
                    </div>
                    <div class="shopping-cart-title">
                        <h4><a href="{{ route('front.product.details', $product->slug) }}">{{ $product->name }}</a>
                        </h4>
                        <h3><span>{{ $item->quantity }} × </span>{{ getProductPrice($item->product_id) }}</h3>
                    </div>
                    <div class="shopping-cart-delete">
                        <a href="#"><i class="far fa-times"></i></a>
                    </div>
                </li>
            @endforeach

        </ul>
        <div class="shopping-cart-footer">
            <div class="shopping-cart-total">
                <h4>Total <span>Rs.{{ $total }}</span></h4>
            </div>
            <div class="shopping-cart-button">
                <a href="{{ route('cart.list') }}">View cart</a>
                <a href="{{ route('cart.shipping') }}">Checkout</a>
            </div>
        </div>
    @else
        <h5> No Cart Items </h5>
    @endif
@endif
