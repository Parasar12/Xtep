@php
    // Navigation data. Every lookup falls back to an empty collection so the
    // header still renders when categories or settings have been wiped.
    try {
        $xtNavAll = \App\Models\Category::where('is_active', 1)->orderBy('ordering', 'asc')->get();
    } catch (\Throwable $e) {
        $xtNavAll = collect();
    }
    $xtNavMain = $xtNavAll->where('parent_id', 0)->values();
    $xtNavChildren = $xtNavAll->where('parent_id', '!=', 0)->groupBy('parent_id');
    $xtNavHome = collect(getShowInHomeCategories() ?? [])->where('parent_id', 0)->take(4);
    $xtLogo = getSiteLogo();
    $xtTitle = getSiteTitle() ?: 'XTEP';
    $xtCartCount = (int) countCartItems();
@endphp

<header id="header" class="xt-header">
    <!-- Topbar -->
    <div class="xt-topbar">
        <div class="xt-wrap">
            <div>{{ getWelcomeText() }}</div>
            <div class="xt-topbar__right">
                @if (getPhoneNumber())
                    <a href="tel:{{ getPhoneNumber() }}"><i class="fas fa-phone"></i>{{ getPhoneNumber() }}</a>
                @endif
                @if (getAddress())
                    <span class="xt-topbar__hide-md"><i class="fas fa-map-marker-alt"></i>{{ getAddress() }}</span>
                @endif
                @auth
                    <a href="{{ route('user.login') }}"><i class="fas fa-user"></i>My account</a>
                @else
                    <a href="{{ route('user.login') }}"><i class="fas fa-user"></i>Sign in / Register</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main bar -->
    <div class="xt-bar" id="xtBar">
        <div class="xt-wrap">
            <button type="button" class="xt-icon xt-burger" aria-label="Open menu" data-xt-drawer="open">
                <i class="fas fa-bars"></i>
            </button>

            <a class="xt-logo" href="{{ route('front.index') }}" aria-label="{{ $xtTitle }}">
                @if ($xtLogo)
                    <img src="{{ asset('uploads/logo/' . $xtLogo) }}" alt="{{ $xtTitle }}">
                @else
                    <span class="xt-logo__text">{{ $xtTitle }}</span>
                @endif
            </a>

            <ul class="xt-nav">
                <li><a href="{{ route('front.index') }}" class="{{ request()->routeIs('front.index') ? 'is-active' : '' }}">Home</a></li>
                <li><a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'is-active' : '' }}">Shop</a></li>
                <li>
                    <a href="{{ route('search') }}">Categories <i class="fas fa-chevron-down"></i></a>
                    <ul class="xt-drop">
                        @forelse ($xtNavMain as $navCat)
                            <li><a href="{{ route('search') }}?category={{ $navCat->slug }}">{{ $navCat->name }}</a></li>
                            @foreach ($xtNavChildren->get($navCat->id, collect()) as $navSub)
                                <li class="xt-drop__sub"><a href="{{ route('search') }}?subcategory={{ $navSub->slug }}">{{ $navSub->name }}</a></li>
                            @endforeach
                        @empty
                            <li class="xt-drop__empty">Categories coming soon</li>
                        @endforelse
                    </ul>
                </li>
                @foreach ($xtNavHome as $homeCat)
                    <li><a href="{{ route('search') }}?category={{ $homeCat->slug }}">{{ $homeCat->name }}</a></li>
                @endforeach
                <li><a href="{{ route('front.about') }}" class="{{ request()->routeIs('front.about') ? 'is-active' : '' }}">About</a></li>
                <li><a href="{{ route('front.contact') }}" class="{{ request()->routeIs('front.contact') ? 'is-active' : '' }}">Contact</a></li>
            </ul>

            <div class="xt-icons">
                <button type="button" class="xt-icon" aria-label="Search" data-xt-search-toggle>
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('user.login') }}" class="xt-icon xt-hide-sm" aria-label="My account" title="My account">
                    <i class="far fa-user"></i>
                </a>
                @auth
                    <a href="{{ route('wishlists.index') }}" class="xt-icon xt-hide-sm" aria-label="Wishlist" title="Wishlist">
                        <i class="far fa-heart"></i>
                    </a>
                @endauth
                <a href="{{ route('cart.list') }}" class="xt-icon" aria-label="Cart" title="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="xt-icon__badge" id="countData">{{ $xtCartCount }}</span>
                </a>
            </div>
        </div>

        <!-- Search panel -->
        <div class="xt-search" id="xtSearch">
            <div class="xt-wrap">
                <form method="get" action="{{ route('search') }}">
                    <label class="sr-only" for="xtSearchInput">Search products</label>
                    <input id="xtSearchInput" type="text" name="product" placeholder="Search for shoes, apparel, accessories..." required>
                    <button class="xt-btn xt-btn--dark" type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile drawer -->
    <div class="xt-drawer-mask" data-xt-drawer="close"></div>
    <aside class="xt-drawer" aria-label="Menu">
        <div class="xt-drawer__head">
            <a class="xt-logo" href="{{ route('front.index') }}">
                @if ($xtLogo)
                    <img src="{{ asset('uploads/logo/' . $xtLogo) }}" alt="{{ $xtTitle }}">
                @else
                    <span class="xt-logo__text">{{ $xtTitle }}</span>
                @endif
            </a>
            <button type="button" class="xt-icon" aria-label="Close menu" data-xt-drawer="close"><i class="fas fa-times"></i></button>
        </div>
        <div class="xt-drawer__body">
            <form class="xt-drawer__search" method="get" action="{{ route('search') }}">
                <input type="search" name="product" placeholder="Search products" aria-label="Search products" required>
                <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
            </form>
            <a class="xt-drawer__link" href="{{ route('front.index') }}">Home</a>
            <a class="xt-drawer__link" href="{{ route('search') }}">Shop all</a>
            @if ($xtNavMain->isNotEmpty())
                <div class="xt-drawer__label">Categories</div>
                @foreach ($xtNavMain as $navCat)
                    <a class="xt-drawer__link" href="{{ route('search') }}?category={{ $navCat->slug }}">{{ $navCat->name }} <i class="fas fa-chevron-right"></i></a>
                    @foreach ($xtNavChildren->get($navCat->id, collect()) as $navSub)
                        <a class="xt-drawer__sublink" href="{{ route('search') }}?subcategory={{ $navSub->slug }}">{{ $navSub->name }}</a>
                    @endforeach
                @endforeach
            @endif
            <div class="xt-drawer__label">More</div>
            <a class="xt-drawer__link" href="{{ route('front.about') }}">About</a>
            <a class="xt-drawer__link" href="{{ route('front.contact') }}">Contact</a>
            <a class="xt-drawer__link" href="{{ route('cart.list') }}"><span>Cart (<span id="cartData">{{ $xtCartCount }}</span>)</span></a>
            @auth
                <a class="xt-drawer__link" href="{{ route('wishlists.index') }}">Wishlist</a>
                <a class="xt-drawer__link" href="{{ route('user.login') }}">My account</a>
            @else
                <a class="xt-drawer__link" href="{{ route('user.login') }}">Sign in / Register</a>
            @endauth
        </div>
    </aside>

    <script>
        (function () {
            var bar = document.getElementById('xtBar');
            var search = document.getElementById('xtSearch');
            window.addEventListener('scroll', function () {
                if (bar) bar.classList.toggle('is-scrolled', window.pageYOffset > 10);
            }, { passive: true });
            document.addEventListener('click', function (e) {
                var t = e.target.closest('[data-xt-search-toggle]');
                if (t && search) {
                    search.classList.toggle('is-open');
                    if (search.classList.contains('is-open')) {
                        var input = document.getElementById('xtSearchInput');
                        if (input) input.focus();
                    }
                    return;
                }
                var d = e.target.closest('[data-xt-drawer]');
                if (d) {
                    document.body.classList.toggle('xt-drawer-open', d.getAttribute('data-xt-drawer') === 'open');
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    document.body.classList.remove('xt-drawer-open');
                    if (search) search.classList.remove('is-open');
                }
            });
        })();
    </script>
</header>
