@foreach ($products as $product)
    <div class="col-xl-3 col-lg-4 col-md-4 col-6 col-sm-4 wow tmFadeInUp">
        <div class="single-product-wrap mb-50">
            <div class="product-img-action-wrap mb-10">
                <div class="product-img product-img-zoom">
                    <a href="{{ route('front.product.details', $product->slug) }}">
                        <img class="default-img"
                             src="{{ asset('uploads/products/' . $product->image) }}"
                             alt="{{ $product->name }}">
                        <img class="hover-img"
                             src="{{ asset('uploads/products/' . $product->image) }}"
                             alt="{{ $product->name }}">
                    </a>
                </div>
                @php
                    $attr = $product->productAttributes->where('is_active', true);
                @endphp
                <div class="product-action-1 d-inline-flex">
                    @if(!$attr->count())
                        <button aria-label="Add To Cart">
                            <i class="far fa-shopping-bag"></i>
                        </button>
                    @endif
                    <div id="WishListIcon-{{$product->id}}">
                        @auth()
                            @if(productInWishlist($product->id))
                                <button type="button"
                                        onclick="removeFromWishlist({{ $product->id }})"
                                        aria-label="Remove From Wishlist">
                                    <i class="fas fa-heart"></i>
                                </button>
                            @else
                                <button type="button"
                                        onclick="addToWishlist({{ $product->id }})"
                                        aria-label="Add To Wishlist">
                                    <i class="far fa-heart"></i>
                                </button>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            <div class="product-content-wrap">
                <div class="product-category">
                    <a
                        href="{{ route('front.category.products', $product->category->slug) }}">{{ $product->category->name }}</a>
                </div>
                <h2><a
                        href="{{ route('front.product.details', $product->slug) }}">{{ $product->name }}</a>
                </h2>
                <div class="product-price">
                                                        <span
                                                            class="new-price">Rs.{{ getProductPrice($product->id) }}</span>
                </div>
            </div>
        </div>
    </div>
@endforeach
