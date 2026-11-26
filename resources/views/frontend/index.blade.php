@extends('frontend.layouts.master')
@section('meta')
    <meta name="description" content="{{ getSiteTitle() }} - performance running shoes, sportswear and accessories in Nepal. Make it different.">
    <meta name="keywords" content="xtep, xtep nepal, running shoes, carbon plated shoes, sportswear, sneakers, kathmandu">
    <meta property="og:image" content="{{ asset('web/xtep/img/hero-race.jpg') }}">

    <title>{{ getSiteTitle() }} - Make it different</title>
@endsection
@section('content')
    @php
        // Normalise everything the controller passes so empty tables never break the page.
        $sliders = collect($sliders ?? []);
        $features = collect($features ?? []);
        $latestProducts = collect($latestProducts ?? []);
        $featuredProducts = collect($featuredProducts ?? []);
        $productsOnSale = collect($productsOnSale ?? []);
        $topRatedProducts = collect($topRatedProducts ?? []);
        $allCategories = collect($allCategories ?? []);

        // Category tiles: categories flagged "show in home", else the top-level ones.
        $tileCategories = collect($categories ?? []);
        if ($tileCategories->isEmpty()) {
            $tileCategories = $allCategories;
        }
        $tileCategories = $tileCategories->take(5);
        $tileCount = $tileCategories->count();
        $tileClass = $tileCount >= 4 ? 'xt-cats--many' : 'xt-cats--' . $tileCount;

        $tabs = collect([
            ['id' => 'featured', 'label' => 'Featured', 'items' => $featuredProducts],
            ['id' => 'sale', 'label' => 'On sale', 'items' => $productsOnSale],
            ['id' => 'top', 'label' => 'Top rated', 'items' => $topRatedProducts],
        ])->filter(fn($t) => $t['items']->isNotEmpty())->values();

        // Flash sale: only when it is running and its first product still exists.
        $flash = null;
        try {
            if ($flashSale && $flashSale->end_date > now()) {
                $flashItem = optional($flashSale->flashSaleProducts)->first();
                $flashProduct = $flashItem ? \App\Models\Product::find($flashItem->product_id) : null;
                if ($flashProduct) {
                    $flashRegular = (float) \App\Models\ProductMaster::where('product_id', $flashProduct->id)->where('is_active', 1)->value('price');
                    $flash = [
                        'sale' => $flashSale,
                        'product' => $flashProduct,
                        'price' => (float) ($flashItem->price ?? getProductPrice($flashProduct->id)),
                        'regular' => $flashRegular,
                    ];
                }
            }
        } catch (\Throwable $e) {
            $flash = null;
        }

        // Links: the running category if one exists, otherwise the whole shop.
        $shopLink = route('search');
        $runCat = $allCategories->first(fn($c) => stripos($c->name, 'run') !== false);
        $runLink = $runCat ? $shopLink . '?category=' . $runCat->slug : $shopLink;
        $sliderLink = function ($slide) use ($shopLink) {
            if (!empty($slide->category_id)) {
                $cat = \App\Models\Category::find($slide->category_id);
                if ($cat) {
                    return $shopLink . '?category=' . $cat->slug;
                }
            }
            return $shopLink;
        };

        // Brand campaign slides (photos live in public/web/xtep/img).
        $img = fn($f) => asset('web/xtep/img/' . $f);
        $campaign = [
            [
                'img' => 'hero-race.jpg', 'mobile' => 'hero-race-m.jpg', 'pos' => '55% 60%',
                'kicker' => 'Race series', 'title' => 'Make it<br>different',
                'sub' => 'Carbon-plated speed, built for the start line and every kilometre after it.',
                'cta' => 'Shop running', 'link' => $runLink,
            ],
            [
                'img' => 'hero-night.jpg', 'mobile' => 'hero-night-m.jpg', 'pos' => '60% 50%',
                'kicker' => 'Kathmandu runs', 'title' => 'Own<br>the night',
                'sub' => 'Light, fast and ready for wet roads, from the Ring Road to race night.',
                'cta' => 'Shop new arrivals', 'link' => $shopLink,
            ],
            [
                'img' => 'hero-lace.jpg', 'mobile' => 'hero-lace-m.jpg', 'pos' => '50% 50%',
                'kicker' => 'Track & training', 'title' => 'Lace up.<br>Go faster.',
                'sub' => 'Responsive cushioning for tempo days, track sessions and race-day PRs.',
                'cta' => 'Shop the collection', 'link' => $shopLink,
            ],
        ];
    @endphp

    <div class="xt-home">

        {{-- ================= HERO (campaign) ================= --}}
        <section class="xt-hero">
            <div class="swiper xtHeroSwiper">
                <div class="swiper-wrapper">
                    @foreach ($campaign as $slide)
                        <div class="swiper-slide">
                            <div class="xt-hero__slide">
                                <picture>
                                    <source media="(max-width: 767px)" srcset="{{ $img($slide['mobile']) }}">
                                    <img class="xt-hero__bg" src="{{ $img($slide['img']) }}" alt="{{ strip_tags($slide['title']) }}"
                                        style="object-position: {{ $slide['pos'] }}" @if (!$loop->first) loading="lazy" @else fetchpriority="high" @endif>
                                </picture>
                                <div class="xt-wrap">
                                    <div class="xt-hero__content">
                                        <span class="xt-hero__kicker">{{ $slide['kicker'] }}</span>
                                        <h1 class="xt-hero__title">{!! $slide['title'] !!}</h1>
                                        <p class="xt-hero__sub">{{ $slide['sub'] }}</p>
                                        <div class="xt-hero__cta">
                                            <a href="{{ $slide['link'] }}" class="xt-btn xt-btn--red">{{ $slide['cta'] }} <i class="fas fa-arrow-right"></i></a>
                                            <a href="#xt-categories" class="xt-btn xt-btn--ghost">Explore</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="xt-hero__nav">
                <button type="button" class="xt-hero__prev" aria-label="Previous slide"><i class="fas fa-chevron-left"></i></button>
                <button type="button" class="xt-hero__next" aria-label="Next slide"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="xt-hero__scroll" aria-hidden="true"><span></span>Scroll</div>
        </section>

        {{-- ================= FEATURE STRIP ================= --}}
        @if ($features->isNotEmpty())
            <section class="xt-usp">
                <div class="xt-wrap">
                    <div class="xt-usp__grid">
                        @foreach ($features->take(4) as $feature)
                            <div class="xt-usp__item">
                                <div class="xt-usp__icon"><i class="{{ str_replace('far ', 'fas ', $feature->icon ?: 'fas fa-check') }}"></i></div>
                                <div>
                                    <p class="xt-usp__title">{{ $feature->title }}</p>
                                    @if ($feature->sub_title)
                                        <p class="xt-usp__sub">{{ $feature->sub_title }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= CATEGORIES ================= --}}
        <section class="xt-section" id="xt-categories">
            <div class="xt-wrap">
                <div class="xt-head">
                    <div>
                        <span class="xt-eyebrow">Shop by category</span>
                        <h2 class="xt-title">Find your fit</h2>
                    </div>
                    <a href="{{ $shopLink }}" class="xt-link">View all</a>
                </div>
                @if ($tileCount)
                    <div class="xt-cats {{ $tileClass }}">
                        @foreach ($tileCategories as $category)
                            @php
                                $catCount = $category->products ? $category->products->where('is_active', true)->count() : 0;
                                $isCutout = $category->image && preg_match('/\.(png|webp|svg)$/i', $category->image);
                            @endphp
                            <a href="{{ $shopLink }}?category={{ $category->slug }}" class="xt-cat {{ $isCutout ? 'xt-cat--contain' : '' }}">
                                @if ($category->image)
                                    <img src="{{ asset('uploads/categories/' . $category->image) }}" alt="{{ $category->name }}" loading="lazy"
                                        onerror="this.remove()">
                                @endif
                                <div class="xt-cat__body">
                                    <h3 class="xt-cat__name">{{ $category->name }}</h3>
                                    <div class="xt-cat__meta">
                                        {{ $catCount }} {{ Str::plural('product', $catCount) }} <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="xt-empty">
                        <i class="fas fa-th-large"></i>
                        <h3>Categories are on the way</h3>
                        <p>We're setting up the store. Check back soon.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- ================= NEW ARRIVALS ================= --}}
        <section class="xt-section">
            <div class="xt-wrap">
                <div class="xt-head">
                    <div>
                        <span class="xt-eyebrow">Just landed</span>
                        <h2 class="xt-title">New arrivals</h2>
                    </div>
                    <a href="{{ $shopLink }}" class="xt-link">Shop all</a>
                </div>
                @if ($latestProducts->isNotEmpty())
                    <div class="xt-grid">
                        @foreach ($latestProducts as $product)
                            @include('frontend.component.product_card', ['product' => $product, 'badge' => 'New'])
                        @endforeach
                    </div>
                @else
                    <div class="xt-empty">
                        <i class="fas fa-shoe-prints"></i>
                        <h3>New products are coming soon</h3>
                        <p>Our latest shoes and apparel will appear here as soon as they're added.</p>
                        <a href="{{ $shopLink }}" class="xt-btn xt-btn--dark">Browse the shop</a>
                    </div>
                @endif
            </div>
        </section>

        {{-- ================= PRODUCT SPOTLIGHT ================= --}}
        <section class="xt-section">
            <div class="xt-wrap">
                <div class="xt-spot">
                    <div class="xt-spot__media">
                        <img src="{{ $img('spotlight-160x.jpg') }}" alt="XTEP 160X PRO racing shoes" loading="lazy">
                    </div>
                    <div class="xt-spot__body">
                        <span class="xt-eyebrow">Spotlight</span>
                        <h2 class="xt-spot__title">160X PRO</h2>
                        <p class="xt-spot__lead">XTEP's flagship carbon-plated racer. A carbon plate and propulsive cushioning turn every stride into forward motion.</p>
                        <ul class="xt-spot__list">
                            <li><b>Carbon plate</b><span>Snappy, propulsive toe-off</span></li>
                            <li><b>Race-day foam</b><span>Light, high energy return</span></li>
                            <li><b>Breathable upper</b><span>Locked-in fit for long efforts</span></li>
                        </ul>
                        <a href="{{ $runLink }}" class="xt-btn xt-btn--red">Shop running shoes <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= FLASH SALE ================= --}}
        @if ($flash)
            <section class="xt-section">
                <div class="xt-wrap">
                    <div class="xt-flash">
                        <a class="xt-flash__img" href="{{ route('front.product.details', $flash['product']->slug) }}">
                            <img src="{{ asset('uploads/products/' . $flash['product']->image) }}" alt="{{ $flash['product']->name }}">
                        </a>
                        <div class="xt-flash__body">
                            <span class="xt-eyebrow">Limited time</span>
                            <h2 class="xt-flash__title">{{ $flash['sale']->name }}</h2>
                            <div class="xt-flash__name">{{ $flash['product']->name }}</div>
                            <div class="xt-flash__price">
                                Rs. {{ number_format($flash['price']) }}
                                @if ($flash['regular'] > $flash['price'])
                                    <del>Rs. {{ number_format($flash['regular']) }}</del>
                                @endif
                            </div>
                            <div class="xt-count" data-xt-countdown="{{ \Carbon\Carbon::parse($flash['sale']->end_date)->toIso8601String() }}">
                                <div><b data-d>00</b><small>Days</small></div>
                                <div><b data-h>00</b><small>Hours</small></div>
                                <div><b data-m>00</b><small>Mins</small></div>
                                <div><b data-s>00</b><small>Secs</small></div>
                            </div>
                            <div>
                                <a href="{{ route('front.product.details', $flash['product']->slug) }}" class="xt-btn xt-btn--red">Grab the deal <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= FEATURED / ON SALE / TOP RATED ================= --}}
        @if ($tabs->isNotEmpty())
            <section class="xt-section">
                <div class="xt-wrap">
                    <div class="xt-head">
                        <div>
                            <span class="xt-eyebrow">Hand-picked</span>
                            <h2 class="xt-title">Trending now</h2>
                        </div>
                    </div>
                    <div class="xt-tabs" role="tablist">
                        @foreach ($tabs as $tab)
                            <button type="button" role="tab" class="{{ $loop->first ? 'is-active' : '' }}" data-xt-tab="xt-tab-{{ $tab['id'] }}">{{ $tab['label'] }}</button>
                        @endforeach
                    </div>
                    @foreach ($tabs as $tab)
                        <div class="xt-tabpane {{ $loop->first ? 'is-active' : '' }}" id="xt-tab-{{ $tab['id'] }}" role="tabpanel">
                            <div class="xt-grid">
                                @foreach ($tab['items'] as $product)
                                    @include('frontend.component.product_card', ['product' => $product])
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ================= FULL-BLEED STORY BANNER ================= --}}
        <section class="xt-section">
            <div class="xt-story">
                <picture>
                    <source media="(max-width: 767px)" srcset="{{ $img('banner-kathmandu-m.jpg') }}">
                    <img src="{{ $img('banner-kathmandu.jpg') }}" alt="Runners racing through Kathmandu traffic" loading="lazy">
                </picture>
                <div class="xt-wrap">
                    <div class="xt-story__body">
                        <span class="xt-eyebrow xt-eyebrow--light">Made for these roads</span>
                        <h2 class="xt-story__title">From Kathmandu streets<br>to the finish line</h2>
                        <p>Rain, traffic, hills and heat. Our runners train where you train, in gear built to keep up.</p>
                        <a href="{{ $runLink }}" class="xt-btn xt-btn--light">Find your running shoe <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ================= LIFESTYLE MOSAIC ================= --}}
        <section class="xt-section">
            <div class="xt-wrap">
                <div class="xt-head">
                    <div>
                        <span class="xt-eyebrow">On and off the track</span>
                        <h2 class="xt-title">Wear it your way</h2>
                    </div>
                    <a href="{{ $shopLink }}" class="xt-link">Shop the look</a>
                </div>
                <div class="xt-mosaic">
                    <a href="{{ $shopLink }}" class="xt-mosaic__item xt-mosaic__item--tall">
                        <img src="{{ $img('style-wall.jpg') }}" alt="Runners in XTEP gear at the XTEP wall" loading="lazy">
                        <div class="xt-mosaic__cap"><b>Race-day style</b><span>Shop apparel <i class="fas fa-arrow-right"></i></span></div>
                    </a>
                    <a href="{{ $runLink }}" class="xt-mosaic__item">
                        <img src="{{ $img('style-booth.jpg') }}" alt="XTEP racing shoes hanging at the XTEP booth" loading="lazy">
                        <div class="xt-mosaic__cap"><b>Super shoes</b><span>Shop racing <i class="fas fa-arrow-right"></i></span></div>
                    </a>
                    <a href="{{ $shopLink }}" class="xt-mosaic__item">
                        <img src="{{ $img('style-street.jpg') }}" alt="Runner in an XTEP singlet on a Kathmandu street" loading="lazy">
                        <div class="xt-mosaic__cap"><b>Everyday miles</b><span>Shop training <i class="fas fa-arrow-right"></i></span></div>
                    </a>
                </div>
            </div>
        </section>

        {{-- ================= ATHLETES ================= --}}
        <section class="xt-section xt-section--dark">
            <div class="xt-wrap">
                <div class="xt-head">
                    <div>
                        <span class="xt-eyebrow">Proven by athletes</span>
                        <h2 class="xt-title xt-title--light">Train like the best</h2>
                    </div>
                    <p class="xt-head__note">From high-altitude training camps to course records, XTEP is built and tested by world-class runners.</p>
                </div>
                <div class="xt-athletes">
                    <figure class="xt-athlete">
                        <img src="{{ $img('athlete-almaty.jpg') }}" alt="Almaty Marathon 2025 new course record in XTEP 160X 7.0 PRO" loading="lazy">
                        <figcaption><b>New course record</b><span>Almaty Marathon 2025, men's half</span></figcaption>
                    </figure>
                    <figure class="xt-athlete">
                        <img src="{{ $img('athlete-camp.jpg') }}" alt="XTEP athletes on a dusty training run" loading="lazy">
                        <figcaption><b>Training camp</b><span>Altitude miles at sunrise</span></figcaption>
                    </figure>
                    <figure class="xt-athlete">
                        <img src="{{ $img('athlete-road.jpg') }}" alt="Two XTEP athletes running side by side" loading="lazy">
                        <figcaption><b>Tempo days</b><span>Side by side, stride for stride</span></figcaption>
                    </figure>
                    <figure class="xt-athlete">
                        <img src="{{ $img('athlete-pack.jpg') }}" alt="A pack of runners in XTEP shoes" loading="lazy">
                        <figcaption><b>The pack</b><span>Long runs under open skies</span></figcaption>
                    </figure>
                </div>
            </div>
        </section>

        {{-- ================= OFFERS (admin sliders) ================= --}}
        @if ($sliders->isNotEmpty())
            <section class="xt-section">
                <div class="xt-wrap">
                    <div class="xt-head">
                        <div>
                            <span class="xt-eyebrow">Offers</span>
                            <h2 class="xt-title">Latest from the store</h2>
                        </div>
                    </div>
                    <div class="xt-offers xt-offers--{{ min($sliders->count(), 3) }}">
                        @foreach ($sliders->take(3) as $slider)
                            <a href="{{ $sliderLink($slider) }}" class="xt-offer">
                                <img src="{{ asset('uploads/sliders/' . $slider->image) }}" alt="{{ $slider->main_title }}" loading="lazy" onerror="this.remove()">
                                <div class="xt-offer__body">
                                    @if ($slider->sub_title)<span class="xt-offer__kicker">{{ $slider->sub_title }}</span>@endif
                                    <h3>{{ $slider->main_title }}</h3>
                                    @if ($slider->title)<p>{{ $slider->title }}</p>@endif
                                    <span class="xt-offer__cta">Shop now <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ================= PER-CATEGORY ROWS ================= --}}
        @foreach ($allCategories as $category)
            @php
                $catProducts = $category->products
                    ? $category->products->where('is_active', true)->where('show_in_home', true)->sortByDesc('created_at')->take(4)
                    : collect();
            @endphp
            @if ($catProducts->isNotEmpty())
                <section class="xt-section">
                    <div class="xt-wrap">
                        <div class="xt-head">
                            <div>
                                <span class="xt-eyebrow">Category</span>
                                <h2 class="xt-title">{{ $category->name }}</h2>
                            </div>
                            <a href="{{ $shopLink }}?category={{ $category->slug }}" class="xt-link">View all</a>
                        </div>
                        <div class="xt-grid">
                            @foreach ($catProducts as $product)
                                @include('frontend.component.product_card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endforeach

        {{-- ================= REVIEWS (Xtep Nepal - Newroad Showroom, Google) ================= --}}
        {{-- Reviews copied from the Google listing on 2026-11-25. Update the list below when new reviews come in. --}}
        @php
            $xtGoogleUrl = 'https://maps.google.com/?cid=17519356949490562588';
            $xtRating = 3.8;
            $xtReviewCount = 13;
            $xtReviews = [
                ['name' => 'Kushal Tiwari', 'stars' => 5, 'when' => '3 years ago', 'text' => 'Reasonable price, best quality, variety of different sizes and outfits.'],
                ['name' => 'Radhe', 'stars' => 5, 'when' => '2 years ago', 'text' => 'Very nice shoes, more comfortable shoes. Try to buy one more.'],
                ['name' => 'Shishir Subedi', 'stars' => 4, 'when' => 'a year ago', 'text' => 'Good, gave in some discounts as well. Shoes are great.'],
                ['name' => 'Pushpa Raj Adhikari', 'stars' => 4, 'when' => '4 years ago', 'text' => 'It\'s the authorized showroom of Xtep products in Nepal.'],
            ];
        @endphp
        <section class="xt-section">
            <div class="xt-wrap">
                <div class="xt-head">
                    <div>
                        <span class="xt-eyebrow">Reviews</span>
                        <h2 class="xt-title">What runners say</h2>
                    </div>
                    <a class="xt-btn xt-btn--red" href="{{ $xtGoogleUrl }}" target="_blank" rel="noopener">Review us on Google</a>
                </div>

                <div class="xt-reviews__summary">
                    <span class="xt-reviews__score">{{ number_format($xtRating, 1) }}</span>
                    <span class="xt-reviews__stars" aria-label="{{ $xtRating }} out of 5 stars">
                        @for ($i = 1; $i <= 5; $i++)<span class="{{ $i <= round($xtRating) ? 'is-on' : '' }}">&#9733;</span>@endfor
                    </span>
                    <a href="{{ $xtGoogleUrl }}" target="_blank" rel="noopener">{{ $xtReviewCount }} Google reviews &middot; Xtep Nepal, Newroad Showroom</a>
                </div>

                <div class="xt-reviews__grid">
                    @foreach ($xtReviews as $review)
                        <div class="xt-reviews__card">
                            <div class="xt-reviews__stars" aria-label="{{ $review['stars'] }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)<span class="{{ $i <= $review['stars'] ? 'is-on' : '' }}">&#9733;</span>@endfor
                            </div>
                            <p class="xt-reviews__text">&ldquo;{{ $review['text'] }}&rdquo;</p>
                            <div class="xt-reviews__who">
                                <span class="xt-reviews__avatar">{{ mb_substr($review['name'], 0, 1) }}</span>
                                <div>
                                    <strong>{{ $review['name'] }}</strong>
                                    <small>Google review &middot; {{ $review['when'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ================= PAYMENTS ================= --}}
        <section class="xt-section">
            <div class="xt-wrap">
                <div class="xt-head" style="justify-content:center;text-align:center;">
                    <div>
                        <span class="xt-eyebrow">Pay your way</span>
                        <h2 class="xt-title">Accepted payments</h2>
                    </div>
                </div>
                <div class="xt-pay">
                    {{-- Hidden for now: Khalti --}}
                    {{-- <div class="xt-pay__item"><img title="Khalti" alt="Khalti" loading="lazy"
                            src="https://english.onlinekhabar.com/wp-content/uploads/2022/02/khalti-digital-wallet-1024x425.png"></div> --}}
                    {{-- Hidden for now: Esewa --}}
                    {{-- <div class="xt-pay__item"><img title="Esewa" alt="Esewa" loading="lazy"
                            src="https://www.cogenthealth.com.np/images/esewa.png"></div> --}}
                    {{-- Hidden for now: Connect IPS --}}
                    {{-- <div class="xt-pay__item"><img title="Connect IPS" alt="Connect IPS" loading="lazy"
                            src="https://citizenlifenepal.com/wp-content/uploads/2020/05/connectips.png"></div> --}}
                    <div class="xt-pay__item"><img title="Fonepay" alt="Fonepay" loading="lazy"
                            src="https://www.fiscalnepal.com/wp-content/uploads/2021/08/fonepay_logo.png"></div>
                    <div class="xt-pay__item"><img title="Cash on Delivery" alt="Cash on Delivery" loading="lazy"
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmbTnuRmvPZK0b0zrGXJEzoHLJWln8dYoD9Q&s"></div>
                </div>
            </div>
        </section>

    </div>
@endsection

@section('script')
    <script>
        (function () {
            // Hero slider
            if (window.Swiper && document.querySelector('.xtHeroSwiper')) {
                var slideCount = document.querySelectorAll('.xtHeroSwiper .swiper-slide').length;
                new Swiper('.xtHeroSwiper', {
                    loop: slideCount > 1,
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    speed: 1000,
                    autoplay: slideCount > 1 ? { delay: 6000, disableOnInteraction: false } : false,
                    pagination: { el: '.xtHeroSwiper .swiper-pagination', clickable: true },
                    navigation: { nextEl: '.xt-hero__next', prevEl: '.xt-hero__prev' },
                });
                // Text animations only switch on once the slider is running, so the
                // hero copy stays visible if the slider script ever fails to load.
                document.querySelector('.xt-hero').classList.add('is-ready');
            }

            // Product tabs
            document.querySelectorAll('[data-xt-tab]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('[data-xt-tab]').forEach(function (b) { b.classList.remove('is-active'); });
                    document.querySelectorAll('.xt-tabpane').forEach(function (p) { p.classList.remove('is-active'); });
                    btn.classList.add('is-active');
                    var pane = document.getElementById(btn.getAttribute('data-xt-tab'));
                    if (pane) pane.classList.add('is-active');
                });
            });

            // Reveal sections as they scroll into view
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                    });
                }, { rootMargin: '0px 0px -8% 0px' });
                document.querySelectorAll('.xt-home .xt-section').forEach(function (s) { s.classList.add('xt-reveal'); io.observe(s); });
            }

            // Flash sale countdown
            document.querySelectorAll('[data-xt-countdown]').forEach(function (el) {
                var end = new Date(el.getAttribute('data-xt-countdown')).getTime();
                var pad = function (n) { return (n < 10 ? '0' : '') + n; };
                var tick = function () {
                    var diff = Math.max(0, end - Date.now());
                    var s = Math.floor(diff / 1000);
                    el.querySelector('[data-d]').textContent = pad(Math.floor(s / 86400));
                    el.querySelector('[data-h]').textContent = pad(Math.floor(s % 86400 / 3600));
                    el.querySelector('[data-m]').textContent = pad(Math.floor(s % 3600 / 60));
                    el.querySelector('[data-s]').textContent = pad(s % 60);
                };
                tick();
                setInterval(tick, 1000);
            });
        })();
    </script>
@endsection
