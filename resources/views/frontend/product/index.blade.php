@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="">
    <meta name="keywords" content="">

    <title>{{ getSiteTitle() }} - Product List</title>
@endsection
@section('content')
    <style>
        /* Product grid cards (large images, like the reference shop page) */
        .xt-card-col { padding: 0 10px; margin-bottom: 28px; }
        .xt-card { display: block; color: inherit; text-decoration: none; }
        .xt-card:hover { text-decoration: none; color: inherit; }
        .xt-card__img { background: #f1f2f4; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .xt-card__img img { width: 100%; height: 100%; object-fit: contain; transition: transform .35s ease; }
        .xt-card:hover .xt-card__img img { transform: scale(1.05); }
        .xt-card__body { padding: 18px 18px 0; }
        .xt-card__cat { font-size: 11px; letter-spacing: .12em; text-transform: uppercase; color: #8a8f98; font-weight: 600; margin-bottom: 4px; }
        .xt-card__title { font-size: 15px; font-weight: 700; color: #111; margin: 0 0 8px; line-height: 1.3; }
        .xt-card__price { font-size: 17px; font-weight: 800; color: #e8340c; }
        /*#loadingArea {*/
        /*    background-color: #1c2020;*/
        /*    height: 100%;*/
        /*    position: relative;*/
        /*    overflow: hidden;*/
        /*}*/
        /* Sidebar hidden initially */
        /* Sidebar hidden off-screen */


        /* Close button styling */


        @media (min-width:768px) {
            #sidebarContent .close-btn {
                display: none !important;
                /* Ensure it appears above other content */
            }
        }

        /* Sidebar active state for small screens */
        @media (max-width: 767px) {
            #sidebarContent.active {
                left: 0;
            }

            #sidebarContent {
                left: -100%;
            }

            #sidebarContent {
                position: fixed;
                top: 0;
                left: -100%;
                width: 300px;
                height: 100%;
                background-color: #fff;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
                overflow-y: auto;
                transition: left 0.5s ease;
                z-index: 1050;
            }

            #sidebarContent .close-btn {
                display: flex;
                justify-content: end;
                position: absolute;
                top: 10px;
                right: 10px;
                background: transparent;
                border: none;
                font-size: 20px;
                cursor: pointer;
                z-index: 1100;
                /* Ensure it appears above other content */
            }

        }

        /* Close button styling */





        /* Sidebar slides in when active */
        #sidebarContent.active {
            left: 0;
            /* Slide into view */
        }

        /* Remove the d-none class for small screens */



        .loader {
            margin: 0 auto;
            width: 60px;
            height: 50px;
            text-align: center;
            font-size: 10px;
            position: absolute;
            /*top: 50%;*/
            left: 50%;
            -webkit-transform: translateY(-50%) translateX(-50%);
        }

        .loader>div {
            height: 100%;
            width: 8px;
            display: inline-block;
            float: left;
            margin-left: 2px;
            -webkit-animation: delay 0.8s infinite ease-in-out;
            animation: delay 0.8s infinite ease-in-out;
        }

        .loader .bar1 {
            background-color: #754fa0;
        }

        .loader .bar2 {
            background-color: #09b7bf;
            -webkit-animation-delay: -0.7s;
            animation-delay: -0.7s;
        }

        .loader .bar3 {
            background-color: #90d36b;
            -webkit-animation-delay: -0.6s;
            animation-delay: -0.6s;
        }

        .loader .bar4 {
            background-color: #f2d40d;
            -webkit-animation-delay: -0.5s;
            animation-delay: -0.5s;
        }

        .loader .bar5 {
            background-color: #fcb12b;
            -webkit-animation-delay: -0.4s;
            animation-delay: -0.4s;
        }

        .loader .bar6 {
            background-color: #ed1b72;
            -webkit-animation-delay: -0.3s;
            animation-delay: -0.3s;
        }

        @-webkit-keyframes delay {

            0%,
            40%,
            100% {
                -webkit-transform: scaleY(0.05);
            }

            20% {
                -webkit-transform: scaleY(1);
            }
        }

        @keyframes delay {

            0%,
            40%,
            100% {
                transform: scaleY(0.05);
                -webkit-transform: scaleY(0.05);
            }

            20% {
                transform: scaleY(1);
                -webkit-transform: scaleY(1);
            }
        }
    </style>
    {{-- Produt Filter --}}

    <div class="bg-gray-13 bg-md-transparent">
        <div class="container">
            <!-- breadcrumb -->
            <div class="my-md-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1">
                            <a href="{{ route('front.index') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">
                            Shop
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- End breadcrumb -->
        </div>
    </div>


    <div class="container pb-3 border-bottom mb-2">
        <form class="" id="search-form" action="{{ route('search') }}" method="GET">
            <div class="row mb-8">
                <div id="sidebarContent" class="d-none d-md-block col-md-3 col-xl-3 col-wd-2gdot5">
                    <button id="closeSidebar" class="close-btn"><i class="fas fa-times"></i></button>

                    <div class="mt-lg-0 mt-6 mb-2 border border-width-2 border-color-3 borders-radius-6">
                        <!-- List -->
                        <ul id="sidebarNav" class="list-unstyled mb-0 sidebar-navbar">
                            <li>
                                <a class="dropdown-toggle dropdown-toggle-collapse dropdown-title" href="javascript:;"
                                    role="button" data-toggle="collapse" aria-expanded="false"
                                    aria-controls="sidebarNav1Collapse" data-target="#sidebarNav1Collapse">
                                    Show All Categories
                                </a>

                                <div id="sidebarNav1Collapse" class="collapse" data-parent="#sidebarNav">
                                    <ul id="sidebarNav1" class="list-unstyled dropdown-list">
                                        <!-- Main Category List -->
                                        @foreach ($categories as $category)
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('search') }}?category={{ $category->slug }}">{{ $category->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>

                            @php
                                $selectedCategory = request('category');
                                $selectedSubcategory = request('subcategory');

                                if ($selectedSubcategory) {
                                    $subcategory = DB::table('categories')
                                        ->where('slug', $selectedSubcategory)
                                        ->first();
                                    $currentCategory = $subcategory
                                        ? DB::table('categories')
                                            ->where('id', $subcategory->parent_id)
                                            ->first()
                                        : null;
                                } else {
                                    $currentCategory = DB::table('categories')
                                        ->where('slug', $selectedCategory)
                                        ->first();
                                }

                                if (!$currentCategory) {
                                    $currentCategory = $categories->first();
                                }

                                $children = DB::table('categories')
                                    ->where('parent_id', $currentCategory->id)
                                    ->get();
                            @endphp

                            <!-- Show either current category's subcategories or main categories if none selected -->
                            @if ($selectedCategory || $selectedSubcategory)
                                <li>
                                    <a class="dropdown-current active"
                                        href="{{ route('search') }}?category={{ $currentCategory->slug }}">
                                        {{ $currentCategory->name }}
                                    </a>

                                    @if ($children->isNotEmpty())
                                        <ul class="list-unstyled dropdown-list">
                                            <!-- Subcategory List -->
                                            @foreach ($children as $childrenCategory)
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('search') }}?subcategory={{ $childrenCategory->slug }}">
                                                        {{ $childrenCategory->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <ul class="list-unstyled dropdown-list">
                                            <li>
                                                <div class="dropdown-item">No Subcategories</div>
                                            </li>
                                        </ul>
                                    @endif
                                </li>
                            @else
                                <!-- Main Category List if no category or subcategory is active -->
                                <li class="d-none d-lg-block d-md-block">
                                    <a class="dropdown-current active" href="javascript:;">Main Categories</a>
                                    <ul class="list-unstyled dropdown-list">
                                        @foreach ($categories as $category)
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('search') }}?category={{ $category->slug }}">{{ $category->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        </ul>

                        <!-- End List -->
                    </div>
                    <div class="mb-2 px-2">
                        <div class="border-bottom border-color-1 mb-2">
                            <h3 class="section-title section-title__sm mb-0 pb-2 font-size-18">
                                Filters
                            </h3>
                        </div>
                        <div class="border-bottom pb-2 mb-2">
                            <h4 class="font-size-14 mb-2 font-weight-bold">Brands</h4>
                            @if (!empty($brands))
                                @php
                                    $cookieBrandIds = isset($_COOKIE['brand']) ? explode(',', $_COOKIE['brand']) : [];
                                @endphp

                                @foreach ($brands as $index => $brand)
                                    <div class="dform-group d-flex align-items-center justify-content-between mb-2 pb-1">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="brand[]" value="{{ $brand->id }}"
                                                id="check{{ $index }}" onchange="setBrandCookie()"
                                                {{ in_array($brand->id, $cookieBrandIds) ? 'checked' : '' }}
                                                class="custom-control-input ">
                                            <label for="check{{ $index }}"
                                                class="custom-control-label">{{ $brand->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="range-slider">
                            <h4 class="font-size-14 mb-3 font-weight-bold">Price</h4>
                            <!-- Range Slider -->
                            <div id="price-slider" class="mb-2"></div>
                            <p>Price: <span id="price-range"></span></p>
                            <input type="hidden" name='price' id="price-input" />

                            <button type="submit" class="btn px-4 btn-primary-dark-w py-2 rounded-lg">
                                Filter
                            </button>
                            <a href="{{ route('search') }}" onclick="clearBrandCookie();">Clear Filters</a>

                        </div>
                    </div>

                </div>
                <div class="col-xl-9 col-wd-9gdot5">
                    <!-- Shop-control-bar Title -->
                    <div class="d-block d-md-flex flex-center-between mb-3">

                        <p class="font-size-14 text-gray-90 mb-0">
                            {{ $totalProducts }} results
                        </p>
                    </div>
                    <!-- End shop-control-bar Title -->
                    <!-- Shop-control-bar -->
                    <div class="bg-gray-1 flex-center-between borders-radius-9 py-1">
                        <div class="d-xl-none">
                            <!-- Account Sidebar Toggle Button -->
                            <a id="sidebarNavToggler1" class="btn btn-sm py-1 font-weight-normal" href="javascript:;"
                                role="button">
                                <i class="fas fa-sliders-h"></i>
                                <span class="ml-1">Filters</span>
                            </a>

                            <!-- End Account Sidebar Toggle Button -->
                        </div>
                        <div class="px-3 d-none d-xl-block">
                            <ul class="nav nav-tab-shop" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="grid" data-toggle="pill" href="#grid1" role="tab"
                                        aria-controls="grid1" aria-selected="false">
                                        <div class="d-md-flex justify-content-md-center align-items-md-center">
                                            <i class="fa fa-th"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="sm-details-tab" data-toggle="pill"
                                        href="#sm-details-tab1" role="tab" aria-controls="sm-details-tab1"
                                        aria-selected="false">
                                        <div class="d-md-flex justify-content-md-center align-items-md-center">
                                            <i class="fa fa-align-justify"></i>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="lg-details-tab-tab" data-toggle="pill"
                                        href="#lg-details-tab" role="tab" aria-controls="lg-details-tab"
                                        aria-selected="true">
                                        <div class="d-md-flex justify-content-md-center align-items-md-center">
                                            <i class="fa fa-list"></i>
                                        </div>
                                    </a>
                                </li>

                            </ul>
                        </div>
                        <div class="d-flex">
                            <select name="sort_by" class="form-control" onchange="filter()">
                                <option value="">Select Order</option>
                                <option value="oldest"
                                    @isset($sort_by) @if ($sort_by == 'oldest') selected @endif @endisset>
                                    Sort by oldest
                                </option>
                                <option value="newest"
                                    @isset($sort_by) @if ($sort_by == 'newest') selected @endif @endisset>
                                    Sort by latest
                                </option>
                                <option value="price-asc"
                                    @isset($sort_by) @if ($sort_by == 'price-asc') selected @endif @endisset>
                                    Sort by price: low to high
                                </option>
                                <option value="price-desc"
                                    @isset($sort_by) @if ($sort_by == 'price-desc') selected @endif @endisset>
                                    Sort by price: high to low
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade pt-2" id="grid1" role="tabpanel" aria-labelledby="grid"
                            data-target-group="groups">
                            <ul class="row list-unstyled products-group no-gutters">
                                @if (!$products->isEmpty())
                                    <div class="shop-bottom-area">
                                        <div class="row">
                                            @foreach ($products as $product)
                                                {{-- @dd($product) --}}
                                                <li class="col-6 col-lg-4 xt-card-col">
                                                    <a href="{{ route('front.product.details', $product->slug) }}" class="xt-card">
                                                        <div class="xt-card__img">
                                                            <img src="{{ asset('uploads/products/' . $product->image) }}"
                                                                alt="{{ $product->name }}" loading="lazy" />
                                                        </div>
                                                        <div class="xt-card__body">
                                                            <div class="xt-card__cat">{{ $product->category->name ?? '' }}</div>
                                                            <h5 class="xt-card__title">{{ $product->name }}</h5>
                                                            <div class="xt-card__price">Rs. {{ number_format((float) getProductPrice($product->id)) }}</div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <h2 class="text-danger font-size-20 fw-bold text-center">No Products Found </h2>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-pane fade pt-2 show active" id="sm-details-tab1" role="tabpanel"
                            aria-labelledby="sm-details-tab" data-target-group="groups">

                            <ul class="row list-unstyled products-group no-gutters">
                                @if (!$products->isEmpty())
                                    <div class="shop-bottom-area">
                                        <div class="row">
                                            @foreach ($products as $product)
                                                <li class="col-6 col-lg-4 xt-card-col">
                                                    <a href="{{ route('front.product.details', $product->slug) }}" class="xt-card">
                                                        <div class="xt-card__img">
                                                            <img src="{{ asset('uploads/products/' . $product->image) }}"
                                                                alt="{{ $product->name }}" loading="lazy" />
                                                        </div>
                                                        <div class="xt-card__body">
                                                            <div class="xt-card__cat">{{ $product->category->name ?? '' }}</div>
                                                            <h5 class="xt-card__title">{{ $product->name }}</h5>
                                                            <div class="xt-card__price">Rs. {{ number_format((float) getProductPrice($product->id)) }}</div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <h2 class="text-danger font-size-20 fw-bold text-center">No Products Found </h2>
                                @endif
                            </ul>
                        </div>

                        <div class="tab-pane fade pt-2" id="lg-details-tab" role="tabpanel"
                            aria-labelledby="lg-details-tab-tab" data-target-group="groups">
                            <ul class="d-block list-unstyled products-group prodcut-list-view">
                                @if (!$products->isEmpty())
                                    <div class="shop-bottom-area">
                                        <div class="row">
                                            @foreach ($products as $product)
                                                <li class="product-item remove-divider">
                                                    <div class="product-item__outer w-100">
                                                        <div class="product-item__inner remove-prodcut-hover py-4 row">
                                                            <div class="product-item__header col-6 col-md-3">
                                                                <div class="mb-2">
                                                                    <a href="{{ route('front.product.details', $product->slug) }}"
                                                                        class="d-block text-center">
                                                                        <img class="img-fluid"
                                                                            src="{{ asset('uploads/products/' . $product->image) }}"
                                                                            alt="{{ $product->name }}" />
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="product-item__body col-6 col-md-6">
                                                                <div class="pr-lg-10">
                                                                    <div class="mb-2">
                                                                        <a href="{{ route('front.category.products', $product->category->slug ?? '') }}"
                                                                            class="font-size-12 text-gray-5">{{ $product->category->name }}</a>
                                                                    </div>
                                                                    <h5 class="mb-2 product-item__title">
                                                                        <a href="{{ route('front.product.details', $product->slug) }}"
                                                                            class="text-blue font-weight-bold">{{ $product->name }}</a>
                                                                    </h5>
                                                                    <ul
                                                                        class="font-size-12 p-0 text-gray-110 mb-4 d-none d-md-block">
                                                                        {!! Str::limit(strip_tags($product->description), 150, '...') !!}
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="product-item__footer col-md-3 d-md-block">
                                                                <div class="mb-3 d-flex justify-content-between">
                                                                    <div class="prodcut-price mb-2">
                                                                        <div class="text-gray-100 text-center">
                                                                            Rs.{{ getProductPrice($product->id) }}</div>
                                                                    </div>
                                                                    <div class="d-block d-xl-block prodcut-add-cart">
                                                                        <button onclick="addToCart({{ $product->id }})"
                                                                            class="btn-add-cart btn-primary transition-3d-hover">
                                                                            <i class="ec ec-add-to-cart"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                @auth
                                                                    <div class="border-top pt-2 flex-center-between flex-wrap">
                                                                        <div class="WishListIcon-{{ $product->id }}">
                                                                            @if (productInWishlist($product->id))
                                                                                <a onclick="removeFromWishlist({{ $product->id }})"
                                                                                    aria-label="Remove From Wishlist"
                                                                                    class="text-gray-6 font-size-13">
                                                                                    <i
                                                                                        class="fas fa-heart mr-1 font-size-15"></i>
                                                                                    Remove from Wishlist
                                                                                </a>
                                                                            @else
                                                                                <a onclick="addToWishlist({{ $product->id }})"
                                                                                    aria-label="Add To Wishlist"
                                                                                    class="text-gray-6 font-size-13">
                                                                                    <i
                                                                                        class="far fa-heart mr-1 font-size-15"></i>
                                                                                    Add to Wishlist
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <h2 class="text-danger font-size-20 fw-bold text-center">No Products Found </h2>
                                @endif
                            </ul>
                        </div>


                    </div>
                </div>
            </div>
        </form>
    </div>


    {{ $products->appends(['price' => request('price'), 'sort_by' => request('sort_by')])->onEachSide(1)->links() }}
@endsection

@section('script')
    <script>
        function setBrandCookie() {
            console.log('Checkbox changed');
            const checkedCheckboxes = document.querySelectorAll('input[name="brand[]"]:checked');

            if (checkedCheckboxes.length === 0) {
                clearBrandCookie();
                location.reload();

            } else {
                const brandIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.value);
                const brandIdsString = brandIds.join(',');
                document.cookie = `brand=${brandIdsString}; path=/;`;
                console.log('Cookie set:', document.cookie);
                location.reload();
            }
        }


        function clearBrandCookie() {
            document.cookie = "brand=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
        }
    </script>
    <script>
        function addToWishlist(id) {
            const div = $('.WishListIcon-' + id)
            $.ajax({
                url: "{{ route('wishlists.store') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'id': id
                },
                success: function(response) {
                    if (response.response) {
                        toastr.success(response.msg);
                        div.html(response.result)
                        $('#headerWishlist').html(response.wishlist)
                    } else {
                        toastr.error(response.msg)
                        location.reload()
                    }
                }
            })
        }

        function removeFromWishlist(id) {
            const div = $('.WishListIcon-' + id)
            $.ajax({
                url: "{{ route('wishlists.remove') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'id': id
                },
                success: function(response) {
                    if (response.response) {
                        toastr.success(response.msg);
                        div.html(response.result)
                        $('#headerWishlist').html(response.wishlist)
                    } else {
                        toastr.error(response.msg)
                        location.reload()
                    }
                }
            })
        }

        function filter() {
            $('#search-form').submit();
        }

        function priceFilter() {
            $('#search-form').submit();
        }

        function checkWindowSize() {

            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 1500)) {
                    setTimeout(fetchData, 4000);
                }
            });

        }

        function fetchData() {
            // debugger
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            var start = Number($('#start').val());
            var allcount = Number($('#totalrecords').val());
            var rowperpage = Number($('#rowperpage').val());
            start = start + rowperpage;
            var url = window.location.href; // Get the current URL
            var formData = getFormDataFromUrl(url)
            formData.append('start', start)

            if (start <= allcount) {

                $('#start').val(start);
                $('#loadingArea').show()

                $.ajax({
                    url: "{{ route('fetchMoreProducts') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken
                    },
                    data: formData,
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false,
                    success: function(response) {
                        // Add
                        $(".tmFadeInUp:last").after(response.result).show().fadeIn("slow");

                        // Check if the page has enough content or not. If not then fetch records
                        if (parseInt(rowperpage) != parseInt(response.total)) {
                            $('#loadingArea').hide()
                        }
                        checkWindowSize()

                    }
                });
            } else {
                // debugger
                // $('#loadingArea').hide()
            }
        }

        function getFormDataFromUrl(url) {
            var query = url.split('?')[1]; // Extract data after "?"
            var params = new URLSearchParams(query); // Create URLSearchParams object

            // Convert URLSearchParams object to a regular FormData object
            var formData = new FormData();
            params.forEach(function(value, key) {
                formData.append(key, value);
            });

            return formData;
        }

        $(function() {

            checkWindowSize()

            $(".page-link").on('click', function(event) {
                event.preventDefault()

                // get href link  as href_links

                const href = $(this).attr('href')

                // get current url

                const current_url = location.href

                // get after ? from url as params

                const index = current_url.slice(current_url.indexOf('?') + 1);

                // append params in  href_links as search_url

                const url = `${href}&${index}`;

                // request from search_url

                if (href) {
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {},
                        success: function(response) {

                            // replace current dom from response dom
                            $('body').html(response)
                        },
                    })
                }


            })
        })


        const slider = document.getElementById('price-slider');
        const priceRange = document.getElementById('price-range');
        const priceInput = document.getElementById('price-input');

        noUiSlider.create(slider, {
            start: [{{ ($prices ?? collect())->min('price') ?? 0 }}, {{ ($prices ?? collect())->max('price') ?? 0 }}],
            connect: true,
            range: {
                'min': {{ ($prices ?? collect())->min('price') ?? 0 }},
                'max': {{ max(($prices ?? collect())->max('price') ?? 0, (($prices ?? collect())->min('price') ?? 0) + 1) }}
            },
            format: {
                to: function(value) {
                    return 'Rs.' + parseFloat(value).toFixed(2);
                },
                from: function(value) {
                    return Number(value.replace('Rs. ', ''));
                }
            }
        });

        // Update displayed range and hidden input whenever slider changes
        slider.noUiSlider.on('update', (values, handle, unencoded) => {
            priceRange.innerHTML = values.join(' - ');
            priceInput.value = `${unencoded[0].toFixed(2)}-${unencoded[1].toFixed(2)}`;
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarToggler = document.getElementById("sidebarNavToggler1");
            const sidebarContent = document.getElementById("sidebarContent");
            const closeSidebar = document.getElementById("closeSidebar");

            // Toggle sidebar visibility only on small screens
            sidebarToggler.addEventListener("click", function() {
                if (window.innerWidth < 768) { // `md` size is typically 768px and below
                    sidebarContent.classList.toggle("active");
                    sidebarContent.classList.toggle("d-none");
                }
            });

            // Close button to hide sidebar on small screens
            closeSidebar.addEventListener("click", function() {
                if (window.innerWidth < 768) {
                    sidebarContent.classList.remove("active");
                    sidebarContent.classList.add("d-none");
                }
            });
        });
    </script>
@endsection
