@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }}-{{ $product->name }}</title>
    <style>
        /* Remove the arrow buttons in Chrome, Safari, Edge, Opera */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Remove the arrow buttons in Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection

@section('content')
    <!-- breadcrumb -->
    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">

                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a href="{{ route('front.index') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a href="{{ route('search') }}">Shop</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a
                                href="{{ route('search') }}?category={{ $product->category->slug }}">{{ $product->category->name }}</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                            {{ $product->name }}</li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>
    <!-- End breadcrumb -->
    <div class="container">
        <!-- Single Product Body -->
        <div class="mb-xl-14 mb-6">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    {{-- <div id="sliderSyncingNav" class="js-slick-carousel u-slick mb-2"
                     data-infinite="true"
                     data-arrows-classes="d-none d-lg-inline-block u-slick__arrow-classic u-slick__arrow-centered--y rounded-circle"
                     data-arrow-left-classes="fas fa-arrow-left u-slick__arrow-classic-inner u-slick__arrow-classic-inner--left ml-lg-2 ml-xl-4"
                     data-arrow-right-classes="fas fa-arrow-right u-slick__arrow-classic-inner u-slick__arrow-classic-inner--right mr-lg-2 mr-xl-4"
                     data-nav-for="#sliderSyncingThumb">

                    <!-- Gallery Images -->
                    @foreach ($gallery as $img)
                        <div class="js-slide">
                            <img class="img-fluid w-100" src="{{ $img }}" alt="Gallery Image">
                        </div>
                    @endforeach
                </div>

                <div id="sliderSyncingThumb"
                     class="js-slick-carousel u-slick u-slick--slider-syncing u-slick--slider-syncing-size u-slick--gutters-1 u-slick--transform-off"
                     data-infinite="true"
                     data-slides-show="5"
                     data-is-thumbs="true"
                     data-nav-for="#sliderSyncingNav">

                    @foreach ($gallery as $img)
                        <div class="js-slide" style="cursor: pointer;">
                            <img class="img-fluid" src="{{ $img }}" alt="Gallery Thumbnail Image">
                        </div>
                    @endforeach
                </div> --}}
                    <div class="product-details-slider-wrap">
                        <div class="pro-dec-big-img-slider">
                            <div class="single-big-img-style">
                                <div class="pro-details-big-img product_featured_image">
                                    <a class="img-popup" href="{{ asset('uploads/products/' . $product->image) }}">
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" alt="">
                                    </a>
                                </div>
                            </div>

                            @foreach ($gallery as $img)
                                @if ($loop->first)
                                @else
                                    <div class="single-big-img-style">
                                        <div class="pro-details-big-img">
                                            <a class="img-popup" href="{{ $img }}">
                                                <img src="{{ $img }}" alt="{{ $img }}">
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                        <div class="product-dec-slider-small product-dec-small-style1">

                            @foreach ($gallery as $gal)
                                <div class="product-dec-small">
                                    <img src="{{ $gal }}" alt="{{ $gal }}">
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <div class="col-md-5 mb-md-6 mb-lg-0">
                    <form method="POST" action="{{ route('cart.store') }}" id="add-to-cart">
                        @csrf
                        <div class="mb-2">
                            <div class="border-bottom pb-md-1 pb-3">
                                <a href="#" class="font-size-12 text-gray-5 mb-2 d-inline-block">Tablets</a>
                                <h2 class="font-size-25 text-lh-1dot2">{{ $product->name }}
                                </h2>
                                <input type="hidden" value="{{ $product->id }}" name="product_id">

                                @if ($product->brand_id)
                                    <div class="text-gray-9 font-size-14">
                                        <span> Brand:
                                            {{ $product->brand->name }}
                                        </span>
                                    </div>
                                @endif
                                {{-- <div class="mb-2">
                                <a class="d-inline-flex align-items-center small font-size-15 text-lh-1" href="#">
                                    <div class="text-warning mr-2">
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="fas fa-star"></small>
                                        <small class="far fa-star text-muted"></small>
                                    </div>
                                    <span class="text-secondary font-size-13">(3 customer reviews)</span>
                                </a>
                            </div> --}}
                                {{-- <div class="d-md-flex align-items-center">

                                <div class="text-gray-9 font-size-14">Availability: <span
                                        class="text-green font-weight-bold">26 in stock</span></div>
                            </div> --}}
                            </div>
                            @php
                                $attr = \App\Models\ProductAttribute::where([
                                    ['product_id', $product->id],
                                    ['is_active', true],
                                ])->get();
                            @endphp


                            <div class="mb-2 font-size-14 text-gray-110">
                                {!! $product->description !!}
                            </div>

                            <div class="border-top border-bottom py-3">
                                <div class="d-flex align-items-center">
                                    @if (sizeof($attr) > 0)
                                        <!-- Select -->
                                        <div class="container-fluid row">
                                            @foreach ($attr as $key => $pa)
                                                @php
                                                    $attribute = \App\Models\Attribute::find($pa->attribute_id)->name;
                                                    $attributeValues = $pa->value;
                                                @endphp
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="attribute"
                                                        value="{{ $pa->id }}"
                                                        data-featured-image="{{ asset('uploads/products/' . $pa->featured_image) }}"
                                                        onclick="getAttributePrice({{ $product->id }}, {{ $pa->id }})"
                                                        id="flexRadioDefault-{{ $pa->id }}"
                                                        <?= $key == 0 ? 'checked' : '' ?>>
                                                    <label class="form-check-label"
                                                        for="flexRadioDefault-{{ $pa->id }}">
                                                        {{ $attribute }} -- [{{ $attributeValues }}]
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- End Select -->
                                    @endif
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="d-flex align-items-baseline">
                                    @if (sizeof($attr) > 0)
                                        <span class="font-size-36 text-decoration-none" id="AttributeDynamicPrice">
                                            Rs. {{ getProductVariantPrice($product->id, $attr[0]->id) }}
                                        </span>
                                    @else
                                        <span class="font-size-36 text-decoration-none">
                                            Rs.{{ getProductPrice($product->id) }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                            <div class="d-flex justify-content-between">
                                @auth
                                    <div id="WishListIcon-{{ $product->id }}">
                                        @if (productInWishlist($product->id))
                                            <a onclick="removeFromWishlist({{ $product->id }})"
                                                aria-label="Remove From Wishlist" class="text-gray-6 font-size-13 ">
                                                <i class="fas fa-heart mr-1 font-size-15"></i>
                                                Remove from Wishlist
                                            </a>
                                        @else
                                            <a onclick="addToWishlist({{ $product->id }})" aria-label="Add To Wishlist"
                                                class="text-gray-6 font-size-13 ">
                                                <i class="far fa-heart mr-1 font-size-15"></i>
                                                Add
                                                to Wishlist
                                            </a>
                                        @endif
                                    </div>
                                @endauth
                                <p><strong>SKU</strong>: {{ $product->sku }}</p>

                            </div>
                            <div class="d-md-flex align-items-end mb-3">

                                <div class="max-width-150 mb-4 mb-md-0">
                                    <h6 class="font-size-14">Quantity</h6>
                                    <!-- Quantity -->
                                    <div class="border rounded-pill py-2 px-3 border-color-1">
                                        <div class="js-quantity row align-items-center">
                                            <div class="col">
                                                <input
                                                    class="js-result form-control h-auto border-0 rounded p-0 shadow-none"
                                                    type="number" max="{{ getProductQuantity($product->id) }}"
                                                    name="quantity" value="1" id="quantity" min="1"
                                                    style="-moz-appearance: textfield; appearance: textfield;">
                                            </div>

                                            <div class=" col-auto pr-1">
                                                <a class="js-minus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0"
                                                    href="javascript:;">
                                                    <small class="fas fa-minus btn-icon__inner"></small>
                                                </a>
                                                <a class="js-plus btn btn-icon btn-xs btn-outline-secondary rounded-circle border-0"
                                                    href="javascript:;">
                                                    <small class="fas fa-plus btn-icon__inner"></small>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Quantity -->
                                </div>
                                <div class="ml-md-3">
                                    <button type="submit" class="btn px-5 btn-primary-dark transition-3d-hover"><i
                                            class="ec ec-add-to-cart mr-2 font-size-20"></i> Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <div class="border-bottom border-color-1 mb-5">
                        <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">Latest Products</h3>
                    </div>
                    <ul class="list-unstyled">
                        @foreach ($latestProduct as $latest)
                            <li class="mb-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <a href="{{ route('front.product.details', $latest->slug) }}"
                                            class="d-block width-75">
                                            <img class="img-fluid"
                                                src="{{ asset('uploads/products/' . $latest->image) }}"
                                                alt="{{ $latest->name }}">
                                        </a>
                                    </div>
                                    <div class="col">
                                        <h3 class="text-lh-1dot2 font-size-14 mb-0"><a
                                                href="{{ route('front.product.details', $latest->slug) }}">{{ $latest->name }}</a>
                                        </h3>
                                        <div class="font-weight-bold">
                                            <ins class="font-size-15 text-red text-decoration-none d-block">Rs.
                                                {{ getNewProductPrice($latest->id) }}</ins>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
            </div>
        </div>
        <!-- End Single Product Body -->
        <!-- Single Product Tab -->
        <div class="mb-8">
            <div class="position-relative position-md-static px-md-6">
                <ul class="nav nav-classic nav-tab nav-tab-lg justify-content-xl-center flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble border-0 pb-1 pb-xl-0 mb-n1 mb-xl-0"
                    id="pills-tab-8" role="tablist">
                    <!-- <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                                                                                                                                                    <a class="nav-link active" id="Jpills-one-example1-tab" data-toggle="pill"
                                                                                                                                                        href="#Jpills-one-example1" role="tab" aria-controls="Jpills-one-example1"
                                                                                                                                                        aria-selected="true">Accessories</a>
                                                                                                                                                </li> -->
                    <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                        <a class="nav-link active" id="Jpills-two-example1-tab" data-toggle="pill"
                            href="#Jpills-two-example1" role="tab" aria-controls="Jpills-two-example1"
                            aria-selected="false">Features</a>
                    </li>
                    {{-- <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                    <a class="nav-link" id="Jpills-three-example1-tab" data-toggle="pill" href="#Jpills-three-example1"
                        role="tab" aria-controls="Jpills-three-example1" aria-selected="false">Specification</a>
                </li> --}}
                    <li class="nav-item flex-shrink-0 flex-xl-shrink-1 z-index-2">
                        <a class="nav-link" id="Jpills-four-example1-tab" data-toggle="pill"
                            href="#Jpills-four-example1" role="tab" aria-controls="Jpills-four-example1"
                            aria-selected="false">Reviews</a>
                    </li>
                </ul>
            </div>
            <!-- Tab Content -->
            <div class="borders-radius-17 border p-4 mt-4 mt-md-0 px-lg-10 py-lg-9">
                <div class="tab-content" id="Jpills-tabContent">
                    <!-- <div class="tab-pane fade active show" id="Jpills-one-example1" role="tabpanel"
                                                                                                                                                    aria-labelledby="Jpills-one-example1-tab">
                                                                                                                                                    <div class="row no-gutters">
                                                                                                                                                        <div class="col mb-6 mb-md-0">
                                                                                                                                                            <ul
                                                                                                                                                                class="row list-unstyled products-group no-gutters border-bottom border-md-bottom-0">
                                                                                                                                                                <li
                                                                                                                                                                    class="col-4 col-md-4 col-xl-2gdot5 product-item remove-divider-sm-down border-0">
                                                                                                                                                                    <div class="product-item__outer h-100">
                                                                                                                                                                        <div class="remove-prodcut-hover product-item__inner px-xl-4 p-3">
                                                                                                                                                                            <div class="product-item__body pb-xl-2">
                                                                                                                                                                                <div class="mb-2 d-none d-md-block"><a
                                                                                                                                                                                        href="../shop/product-categories-7-column-full-width.html"
                                                                                                                                                                                        class="font-size-12 text-gray-5">Speakers</a></div>
                                                                                                                                                                                <h5 class="mb-1 product-item__title d-none d-md-block"><a
                                                                                                                                                                                        href="#" class="text-blue font-weight-bold">Wireless
                                                                                                                                                                                        Audio System Multiroom 360 degree Full base audio</a>
                                                                                                                                                                                </h5>
                                                                                                                                                                                <div class="mb-2">
                                                                                                                                                                                    <a href="../shop/single-product-fullwidth.html"
                                                                                                                                                                                        class="d-block text-center"><img class="img-fluid"
                                                                                                                                                                                            src="../../assets/img/212X200/img1.jpg"
                                                                                                                                                                                            alt="Image Description"></a>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="flex-center-between mb-1 d-none d-md-block">
                                                                                                                                                                                    <div class="prodcut-price">
                                                                                                                                                                                        <div class="text-gray-100">$685,00</div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </li>
                                                                                                                                                                <li class="col-4 col-md-4 col-xl-2gdot5 product-item remove-divider-sm-down">
                                                                                                                                                                    <div class="product-item__outer h-100">
                                                                                                                                                                        <div
                                                                                                                                                                            class="remove-prodcut-hover add-accessories product-item__inner px-xl-4 p-3">
                                                                                                                                                                            <div class="product-item__body pb-xl-2">
                                                                                                                                                                                <div class="mb-2 d-none d-md-block"><a
                                                                                                                                                                                        href="../shop/product-categories-7-column-full-width.html"
                                                                                                                                                                                        class="font-size-12 text-gray-5">Speakers</a></div>
                                                                                                                                                                                <h5 class="mb-1 product-item__title d-none d-md-block"><a
                                                                                                                                                                                        href="#" class="text-blue font-weight-bold">Tablet White
                                                                                                                                                                                        EliteBook Revolve 810 G2</a></h5>
                                                                                                                                                                                <div class="mb-2">
                                                                                                                                                                                    <a href="../shop/single-product-fullwidth.html"
                                                                                                                                                                                        class="d-block text-center"><img class="img-fluid"
                                                                                                                                                                                            src="../../assets/img/212X200/img2.jpg"
                                                                                                                                                                                            alt="Image Description"></a>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="flex-center-between mb-1 d-none d-md-block">
                                                                                                                                                                                    <div
                                                                                                                                                                                        class="prodcut-price d-flex align-items-center position-relative">
                                                                                                                                                                                        <ins
                                                                                                                                                                                            class="font-size-20 text-red text-decoration-none">$1999,00</ins>
                                                                                                                                                                                        <del
                                                                                                                                                                                            class="font-size-12 tex-gray-6 position-absolute bottom-100">$2
                                                                                                                                                                                            299,00</del>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </li>
                                                                                                                                                                <li
                                                                                                                                                                    class="col-4 col-md-4 col-xl-2gdot5 product-item remove-divider-sm-down remove-divider">
                                                                                                                                                                    <div class="product-item__outer h-100">
                                                                                                                                                                        <div
                                                                                                                                                                            class="remove-prodcut-hover add-accessories product-item__inner px-xl-4 p-3">
                                                                                                                                                                            <div class="product-item__body pb-xl-2">
                                                                                                                                                                                <div class="mb-2 d-none d-md-block"><a
                                                                                                                                                                                        href="../shop/product-categories-7-column-full-width.html"
                                                                                                                                                                                        class="font-size-12 text-gray-5">Speakers</a></div>
                                                                                                                                                                                <h5 class="mb-1 product-item__title d-none d-md-block"><a
                                                                                                                                                                                        href="#" class="text-blue font-weight-bold">Purple Solo
                                                                                                                                                                                        2 Wireless</a></h5>
                                                                                                                                                                                <div class="mb-2">
                                                                                                                                                                                    <a href="../shop/single-product-fullwidth.html"
                                                                                                                                                                                        class="d-block text-center"><img class="img-fluid"
                                                                                                                                                                                            src="../../assets/img/212X200/img3.jpg"
                                                                                                                                                                                            alt="Image Description"></a>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="flex-center-between mb-1 d-none d-md-block">
                                                                                                                                                                                    <div class="prodcut-price">
                                                                                                                                                                                        <div class="text-gray-100">$685,00</div>
                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </li>
                                                                                                                                                            </ul>
                                                                                                                                                            <div
                                                                                                                                                                class="form-check pl-4 pl-md-0 ml-md-4 mb-2 pb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
                                                                                                                                                                <input class="form-check-input" type="checkbox" value="" id="inlineCheckbox1"
                                                                                                                                                                    checked disabled>
                                                                                                                                                                <label class="form-check-label mb-1" for="inlineCheckbox1">
                                                                                                                                                                    <strong>This product: </strong> Ultra Wireless S50 Headphones S50 with
                                                                                                                                                                    Bluetooth - <span class="text-red font-size-16">$35.00</span>
                                                                                                                                                                </label>
                                                                                                                                                            </div>
                                                                                                                                                            <div
                                                                                                                                                                class="form-check pl-4 pl-md-0 ml-md-4 mb-2 pb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
                                                                                                                                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                                                                                                                                                    value="option1" checked>
                                                                                                                                                                <label class="form-check-label mb-1 text-blue" for="inlineCheckbox2">
                                                                                                                                                                    <span class="text-decoration-on cursor-pointer-on">Universal Headphones Case
                                                                                                                                                                        in Black</span> - <span class="text-red font-size-16">$159.00</span>
                                                                                                                                                                </label>
                                                                                                                                                            </div>
                                                                                                                                                            <div
                                                                                                                                                                class="form-check pl-4 pl-md-0 ml-md-4 mb-2 pb-2 pb-md-0 mb-md-0 border-bottom border-md-bottom-0">
                                                                                                                                                                <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                                                                                                                                                    value="option2" checked>
                                                                                                                                                                <label class="form-check-label mb-1 text-blue" for="inlineCheckbox3">
                                                                                                                                                                    <span class="text-decoration-on cursor-pointer-on">Headphones USB
                                                                                                                                                                        Wires</span> - <span class="text-red font-size-16">$50.00</span>
                                                                                                                                                                </label>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <div class="col-md-auto">
                                                                                                                                                            <div class="mr-xl-15">
                                                                                                                                                                <div class="mb-3">
                                                                                                                                                                    <div class="text-red font-size-26 text-lh-1dot2">$244.00</div>
                                                                                                                                                                    <div class="text-gray-6">for 3 item(s)</div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="#"
                                                                                                                                                                    class="btn btn-sm btn-block btn-primary-dark btn-wide transition-3d-hover">Add
                                                                                                                                                                    all to cart</a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                </div> -->
                    <div class="tab-pane active show fade" id="Jpills-two-example1" role="tabpanel"
                        aria-labelledby="Jpills-two-example1-tab">
                        <div class="row gx-4 gy-4">
                            @foreach ($features as $feature)
                                <div class="col-md-3 col-lg-3 col-6 text-center">
                                    <div class="mb-1">
                                        <i style="font-size: 1.5rem; font-weight:300;" class="{{ $feature->icon }}"></i>
                                    </div>
                                    <div class="">
                                        <h6 class="fw-bold">{{ $feature->title }}</h6>
                                        <span class="font-size-12">{{ $feature->sub_title }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- <div class="tab-pane fade" id="Jpills-three-example1" role="tabpanel"
                    aria-labelledby="Jpills-three-example1-tab">
                    {!!$product->specifications!!}
                </div> --}}
                    <div class="tab-pane fade" id="Jpills-four-example1" role="tabpanel"
                        aria-labelledby="Jpills-four-example1-tab">
                        <div class="fb-comments" data-href="{{ URL::current() }}" data-width="100%" data-numposts="5">
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Tab Content -->
        </div>
        <!-- End Single Product Tab -->
        <!-- Related products -->
        <div class="mb-6">
            <div
                class="d-flex justify-content-between align-items-center border-bottom border-color-1 flex-lg-nowrap flex-wrap mb-4">
                <h3 class="section-title mb-0 pb-2 font-size-22">Related products</h3>
            </div>
            <ul class="row list-unstyled products-group no-gutters">
                @foreach ($relatedProducts as $rp)
                    <div class="col-md-2 col-lg-2 col-md-3 col-6 col-xl-2 d-md-flex d-wd-block">
                        <div class="product-item__outer h-100 w-100 prodcut-box-shadow">
                            <div class="product-item__inner bg-white p-3" style="border-radius: 5px !important;">
                                <div class="product-item__body pb-xl-2">
                                    <div class="mb-2"><a href="" class="font-size-12 text-gray-5"><a
                                                href="{{ route('search') }}?category={{ $rp->category->slug }}"
                                                class="font-size-12 text-gray-5">{{ $rp->category->name }}</a>

                                    </div>
                                    <h5 class="mb-1 product-item__title"><a
                                            href="{{ route('front.product.details', $rp->slug) }}"
                                            class="text-blue font-weight-bold">{{ $rp->name }}</a></h5>
                                    <div class="mb-2">
                                        <a href="{{ route('front.product.details', $rp->slug) }}"
                                            class="d-block text-center"><img class="img-fluid"
                                                src="{{ asset('uploads/products/' . $rp->image) }}"
                                                alt="{{ $rp->name }}"></a>
                                    </div>
                                    <div class="flex-center-between mb-1">
                                        <div class="prodcut-price">
                                            <div class="text-gray-100">Rs. {{ getNewProductPrice($rp->id) }}
                                            </div>
                                        </div>
                                        <div class="d-none d-xl-block prodcut-add-cart">

                                            @if (getProductDetail($rp->id)['track_qty'] && getProductDetail($rp->id)['actual_product_qty'] > 0)
                                                <button onclick="addToCart({{ $rp->id }})"
                                                    class="btn-add-cart btn-primary transition-3d-hover"><i
                                                        class="ec ec-add-to-cart"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="product-item__footer"> --}}
                                <div class="border-top pt-2 flex-center-between flex-wrap">

                                    <div id="WishListIcon-{{ $rp->id }}">
                                        @auth
                                            @if (productInWishlist($rp->id))
                                                <a onclick="removeFromWishlist({{ $rp->id }})"
                                                    aria-label="Remove From Wishlist" class="text-gray-6 font-size-13 ">
                                                    <i class="fas fa-heart mr-1 font-size-15"></i>
                                                    Remove from Wishlist
                                                </a>
                                            @else
                                                <a onclick="addToWishlist({{ $rp->id }})" aria-label="Add To Wishlist"
                                                    class="text-gray-6 font-size-13 ">
                                                    <i class="far fa-heart mr-1 font-size-15"></i>
                                                    Add
                                                    to Wishlist
                                                </a>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                {{--
                        </div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach

            </ul>
        </div>

    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-12">
                <div class="elfsight-app-ad007e2c-9910-46d6-beee-93a3a0fedcf5" data-elfsight-app-lazy></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://static.elfsight.com/platform/platform.js" async></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const maxQuantity = parseInt(quantityInput.getAttribute('max'));

            document.querySelector('.js-plus').addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue < maxQuantity) {
                    quantityInput.value = currentValue + 1;
                }
            });

            document.querySelector('.js-minus').addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
        });

        function getAttributePrice(product, attribute) {
            $.ajax({
                url: "{{ route('getProductAttributePrice') }}",
                type: 'GET',
                data: {
                    product,
                    attribute
                },
                success: function(response) {
                    if (response.response) {
                        const price = 'Rs.' + response.result
                        $('#AttributeDynamicPrice').html(price)
                        $('.product_featured_image').html(response.attribute_image)
                        console.log(response.attribute_image)

                        $('#productInStock').html(response.qty)
                    } else {
                        toastr.error(response.msg)
                    }
                }
            })
        }
    </script>
@endsection
