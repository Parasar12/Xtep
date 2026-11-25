<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    @yield('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ url('') }}"/>
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{ getSiteTitle() }}"/>
    <meta property="og:url" content="{{ url('') }}"/>
    <meta property="og:site_name" content="{{ getSiteTitle() }}"/>


    <!-- Add site Favicon -->
    <link rel="icon" href="{{ asset('uploads/logo/' . getSiteLogo()) }}" sizes="32x32"/>
    {{--

    <link rel="stylesheet" href="{{ asset('web/assets/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/vendor/font-cerebrisans.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/vendor/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/vendor/font-medizin.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/plugins/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/plugins/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/plugins/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/plugins/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('web/assets/css/mystyle.css') }}">

    <script src="{{ asset('dashboard/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/validator.js') }}"></script>
    <script src="{{ asset('dashboard/app/app.js') }}"></script>

    <link href="{{ asset('dashboard/css/layout.css') }}" rel="stylesheet" type="text/css" />

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FS2PYVW742"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-FS2PYVW742');
    </script>
 --}}
    <!-- Google tag (gtag.js) -->

    {{-- New Design CSS --}}

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;display=swap"
        rel="stylesheet">

    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('web/assets/css/plugins/magnific-popup.css') }}">

    <link rel="stylesheet" href="{{asset('web/new/vendor/font-awesome/css/fontawesome-all.min.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/css/font-electro.css')}}">

    <link rel="stylesheet" href="{{asset('web/new/vendor/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/vendor/hs-megamenu/src/hs.megamenu.css')}}">
    <link rel="stylesheet"
          href="{{asset('web/new/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/vendor/fancybox/jquery.fancybox.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/vendor/slick-carousel/slick/slick.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}">
    <link rel="stylesheet" href="{{asset('web/new/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('web/assets/css/nouislider.min.css')}}">

    <!-- CSS Electro Template -->
    <link rel="stylesheet" href="{{asset('web/new/css/theme.css')}}">
    <!-- XTEP storefront styles (header, footer, homepage, product cards) -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('web/xtep/xtep.css') }}?v={{ @filemtime(public_path('web/xtep/xtep.css')) }}">
    <link rel="stylesheet" href="{{ asset('web/xtep/xtep-brand.css') }}?v={{ @filemtime(public_path('web/xtep/xtep-brand.css')) }}">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>


    <style>

        .categories-card {
            height: 130px !important;
            width: 140px !important;
        }

        .swiper-slide {
            text-align: center;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>


</head>

<body>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0"
        nonce="JyUxnCEj"></script>
<div class="main-wrapper">
    @include('frontend.layouts.header')
    <main role="main">
        @yield('content')
    </main>
    @include('frontend.layouts.footer')
    <!-- Go to Top -->
    <a class="js-go-to u-go-to" href="#" data-position='{"bottom": 15, "right": 15 }' data-type="fixed"
       data-offset-top="400" data-compensation="#header" data-show-effect="slideInUp"
       data-hide-effect="slideOutDown">
        <span class="fas fa-arrow-up u-go-to__inner"></span>
    </a>
    <!-- End Go to Top -->


    <!-- All JS is here
============================================ -->
    {{-- <script src="{{ asset('web/assets/js/vendor/modernizr-3.6.0.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('web/assets/js/vendor/jquery-3.5.1.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('web/assets/js/vendor/jquery-migrate-3.3.0.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('web/assets/js/vendor/bootstrap.bundle.min.js') }}"></script> --}}

    {{-- <script src="{{ asset('web/assets/js/plugins/jquery-ui.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/clipboard.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/vivus.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/waypoints.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/mouse-parallax.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/scrollup.js') }}"></script>
     --}}
    <!-- Main JS -->





    {{-- new Js --}}

    <!-- JS Global Compulsory -->
    <script src="{{asset('web/new/vendor/jquery/dist/jquery.min.js')}}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="{{asset('web/new/vendor/jquery-migrate/dist/jquery-migrate.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/bootstrap/bootstrap.min.js')}}"></script>

    <!-- JS Implementing Plugins -->
    <script src="{{asset('web/new/vendor/appear.js')}}"></script>
    <script src="{{asset('web/new/vendor/jquery.countdown.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/hs-megamenu/src/hs.megamenu.js')}}"></script>
    <script src="{{asset('web/new/vendor/svg-injector/dist/svg-injector.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')}}">
    </script>
    <script src="{{asset('web/new/vendor/jquery-validation/dist/jquery.validate.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/fancybox/jquery.fancybox.min.js')}}"></script>
    <script src="{{asset('web/new/vendor/typed.js/lib/typed.min.js')}}"></script>
    {{-- <script src="{{asset('web/new/vendor/slick-carousel/slick/slick.js')}}"></script> --}}
    <script src="{{asset('web/new/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>

    <!-- JS Electro -->
    <script src="{{asset('web/new/js/hs.core.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.countdown.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.header.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.hamburgers.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.unfold.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.focus-state.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.malihu-scrollbar.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.validation.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.fancybox.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.onscroll-animation.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.show-animation.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.svg-injector.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.go-to.js')}}"></script>
    <script src="{{asset('web/new/js/components/hs.selectpicker.js')}}"></script>


    <script src="{{ asset('web/assets/js/plugins/jquery-ui-touch-punch.js') }}"></script>

    <script src="{{ asset('web/assets/js/plugins/slick.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/jquery.syotimer.min.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/wow.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/select2.min.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/counterup.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/images-loaded.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/isotope.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/ajax-mail.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/magnific-popup.js') }}"></script>
    <script src="{{ asset('web/assets/js/plugins/svg-inject.min.js') }}"></script>


    <link href="{{ asset('dashboard/plugins/toast/build/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
    <script src="{{ asset('dashboard/plugins/toast/build/toastr.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"></script>
    <script src="{{ asset('dashboard/js/bootbox/bootbox.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/bootbox/bootbox.locales.min.js') }}"></script>
    <script src="{{ asset('dashboard/app/app.js') }}"></script>
    <script src="{{ asset('dashboard/js/app.js') }}"></script>

    <script src="{{ asset('web/assets/js/main.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>


    <!-- Toast -->


    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/wavesurfer.js/3.0.0/wavesurfer.min.js"></script> --}}

    <!-- bootbox -->


    <script src="{{asset('web/new/js/components/hs.slick-carousel.js')}}"></script>
    @if(session('reload'))
        <script>
            window.location.reload();
        </script>
    @endif
    <script>
        /*-------------------------------------
    Product details big image slider
---------------------------------------*/
        $(window).on('load', function () {
            $('.pro-dec-big-img-slider').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                dots: false,
                arrows: false,
                draggable: false,
                fade: false,
                asNavFor: '.product-dec-slider-small , .product-dec-slider-small-2',
            });

            /*---------------------------------------
                Product details small image slider
            -----------------------------------------*/
            $('.product-dec-slider-small').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                asNavFor: '.pro-dec-big-img-slider',
                dots: false,
                focusOnSelect: true,
                fade: false,
                arrows: false,
                responsive: [{
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                    {
                        breakpoint: 767,
                        settings: {
                            slidesToShow: 4,
                        }
                    },
                    {
                        breakpoint: 575,
                        settings: {
                            slidesToShow: 2,
                        }
                    }
                ]
            })
        });

        function cartCount() {
            const cartStoreUrl = "{{ route('cart.cart_count') }}";

            $.ajax({
                url: cartStoreUrl,
                method: "GET",
                success: function (data) {
                    // console.log(data);
                    if (data.status == 1) {
                        // Assuming data.count contains the cart count
                        $("#cartData").text(data.count); // Update the mobile cart count
                        // debugger;
                        $("#countData").text(data.count); // Update the desktop cart count
                    } else {
                        console.error("Error fetching cart count:", data.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("this is error:", error);
                },
            });
        }

        // Function to add item to cart
        function addToCart(product_id) {
            $.ajax({
                url: "{{ route('cart.store') }}",
                method: "POST",
                data: {
                    product_id: product_id,
                    quantity: 1,
                    _token: "{{ csrf_token() }}",
                },
                success: function (data) {
                    if (data.status == 0) {
                        toastr.error(data.error.quantity);
                    } else {
                        toastr.success(data.msg);
                    }
                    cartCount(); // Update the cart count after adding item
                },
                error: function (xhr, status, error) {
                    console.error("Error adding item to cart:", error);
                },
            });
        }


        function addToWishlist(id) {
            const div = $("#WishListIcon-" + id);
            $.ajax({
                url: "{{ route('wishlists.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                },
                success: function (response) {
                    if (response.response) {
                        toastr.success(response.msg);
                        div.html(response.result);
                        $("#headerWishlist").html(response.wishlist);
                    } else {
                        toastr.error(response.msg);
                        location.reload();
                    }
                },
            });
        }

        function removeFromWishlist(id) {
            const div = $("#WishListIcon-" + id);
            $.ajax({
                url: "{{ route('wishlists.remove')  }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                },
                success: function (response) {
                    if (response.response) {
                        toastr.success(response.msg);
                        div.html(response.result);
                        $("#headerWishlist").html(response.wishlist);
                    } else {
                        toastr.error(response.msg);
                        location.reload();
                    }
                },
            });
        }
    </script>
    <script src="{{asset('web/new/js/main.js')}}"></script>
    <!-- bootbox -->
    <!-- JS Plugins Init. -->
    <script>
        function closeModal() {
            $('#addBannerModal').modal('hide'); // This will hide the modal and remove the backdrop

        }

        function changeOrderStatus(id, order_date) {
            // Show the modal
            $('#addBannerModal').modal('show');

            // Populate the hidden fields with the order ID and order date
            $('#order_id').val(id);
            $('#order_date').val(order_date);

            // Handle the form submission within the modal
            $('#orderCancellation').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: "{{ route('order.cancel') }}",
                    method: "POST",
                    data: $(this).serialize(), // Serialize the form data
                    success: function (data) {
                        if (data.response == 0) {
                            toastr.error(data.msg);
                        } else {
                            // Update the order list and hide the modal
                            $("#orders_list").html(data.result);
                            $('#addBannerModal').modal('hide');
                            $('#addBannerModal').on('hidden.bs.modal', function () {
                                $(this).find('form')[0].reset(); // Reset the form if needed
                                $(this).removeData('bs.modal'); // Clear modal data
                            });
                            toastr.success(data.msg);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error Cancelling order:", error);
                    },
                });
            });
        }
    </script>
@yield('script')
</body>

</html>
