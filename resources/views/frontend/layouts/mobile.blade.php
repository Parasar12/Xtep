<div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href="{{ route('front.index') }}">
                    <img src="{{ asset('uploads/logo/' . getSiteLogo()) }}" alt="{{ getSiteTitle() }}">
                </a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">
            <div class="mobile-search search-style-3 mobile-header-border">
                <form method="get" action="{{ route('search') }}">
                    <input type="text" name="product" placeholder="Search…">
                    <button type="submit"><i class="far fa-search"></i></button>
                </form>
            </div>
            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    <ul class="mobile-menu">
                        <li class="menu-item-has-children">
                            <a href="{{ route('front.index') }}">Home</a>
                        </li>
                        <li><a href="{{ route('front.contact') }}">Contact</a></li>
                        <li class="menu-item-has-children">
                            <a href="#">More</a>
                            <ul class="dropdown">
                                <li><a href="{{ route('front.about') }}">About Us</a></li>
                                <li><a href="{{ route('front.faq') }}">Purchase Guide</a></li>
                                <li><a href="{{ route('front.privacy') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('front.terms_conditions') }}">Terms of Service</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- mobile menu end -->
            </div>
            <div class="main-categori-wrap mobile-header-border">
                <a class="categori-button-active-2" href="#">
                    <span class="far fa-bars"></span> Browse Categories <i class="down far fa-chevron-down"></i>
                </a>
                @php $categories = getAllCategories(); @endphp
                @if (!$categories->isEmpty())
                    <div class="categori-dropdown-wrap categori-dropdown-active-small">
                        <ul>
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('search') }}?category={{ $category->slug }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="mobile-header-info-wrap mobile-header-border">
                @guest
                    <div class="single-mobile-header-info">
                        <a href="{{ route('user.login') }}">Log In / Sign Up </a>
                    </div>
                @endguest
                <div class="single-mobile-header-info">
                    <a href="#">{{ getPhoneNumber() }} </a>
                </div>
            </div>
            <div class="mobile-social-icon">
                @foreach (getSocialLinks() as $link)
                    <a class="{{ $link->name }}" href="{{ $link->url }}" target="_blank">
                        <i class="{{ $link->icon_class }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
{{-- <section class="mysection">
    <div class="container">
        <div class="mynav">
            <ul>
                <li>
                    <a href="{{ route('front.about') }}">
                        <i class="far fa-info">
                        </i><span>About</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.contact') }}">
                        <i class="far fa-user">
                        </i><span>Contact</span>
                    </a>
                </li>
                <li>
                    <a class="active" href="{{ route('front.index') }}">
                        <i class="far fa-home-alt">
                        </i><span>home</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.index') }}">
                        <i class="far fa-heart">
                        </i><span>Wishlist</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('front.index') }}">
                        <i class="far fa-shopping-bag">
                        </i><span>Cart</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</section> --}}
<div class="sticky-navbar fixed">
    <div class="sticky-info">
        <a href="{{ route('front.about') }}">
            <i class="far fa-info"></i>About
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ route('front.contact') }}" class="">
            <i class="far fa-user"></i>Contact
        </a>
    </div>
    <div class="sticky-info">
        <a href="{{ route('front.index') }}" class="">
            <i class="far fa-home-alt"></i>home
        </a>
    </div>
    @auth
    <div class="sticky-info">
        <a href="{{ route('front.about') }}" class="">
            <i class="far fa-heart"></i>Wishlist
        </a>
    </div>
    @else
    <div class="sticky-info">
        <a href="{{ route('user.login') }}" class="">
            <i class="far fa-user-lock"></i>Login
        </a>
    </div>
    @endauth
    <div class="sticky-info">
        <a href="{{ route('cart.list') }}" class="">
            <i class="far fa-shopping-bag"></i>Cart
        </a>
    </div>
</div>
