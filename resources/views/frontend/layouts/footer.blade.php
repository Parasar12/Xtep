@php
    $xtFootCats = collect(getShowInHomeCategories() ?? []);
    if ($xtFootCats->isEmpty()) {
        $xtFootCats = collect(getMainCategories() ?? []);
    }
    $xtFootCats = $xtFootCats->take(6);
    $xtFootFeatured = collect(getFeaturedProducts() ?? [])->take(5);
    $xtSocial = collect(getSocialLinks() ?? []);
    $xtFb = getFacebookPage();
    $xtFbHasContent = $xtFb && trim(strip_tags($xtFb, '<iframe><img><a>')) !== '';
    $xtFootLogo = getSiteLogo();

    // Map a social site's name to a Font Awesome brand icon (the stored icon
    // classes come from an older theme and do not all exist here).
    $xtSocialIcon = function ($link) {
        $n = strtolower(($link->name ?? '') . ' ' . ($link->url ?? ''));
        foreach (['facebook' => 'fab fa-facebook-f', 'instagram' => 'fab fa-instagram', 'youtube' => 'fab fa-youtube',
                  'twitter' => 'fab fa-twitter', 'x.com' => 'fab fa-twitter', 'linkedin' => 'fab fa-linkedin-in',
                  'tiktok' => 'fas fa-music', 'whatsapp' => 'fab fa-whatsapp', 'viber' => 'fab fa-viber'] as $k => $icon) {
            if (str_contains($n, $k)) return $icon;
        }
        return 'fas fa-globe';
    };
@endphp

<footer class="xt-footer">
    <div class="xt-wrap">
        <div class="xt-footer__top">
            <div class="xt-footer__brand">
                <a href="{{ route('front.index') }}">
                    @if ($xtFootLogo)
                        <img src="{{ asset('uploads/logo/' . $xtFootLogo) }}" alt="{{ getSiteTitle() }}">
                    @else
                        <span class="xt-logo__text">{{ getSiteTitle() ?: 'XTEP' }}</span>
                    @endif
                </a>
                <p>{{ getWelcomeText() ?: 'Performance footwear and apparel engineered for every dimension of your movement.' }}</p>
                @if ($xtSocial->isNotEmpty())
                    <div class="xt-social">
                        @foreach ($xtSocial as $link)
                            <a href="{{ $link->url }}" target="_blank" rel="noopener" aria-label="{{ $link->name }}" title="{{ $link->name }}">
                                <i class="{{ $xtSocialIcon($link) }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h6>Shop</h6>
                <ul>
                    <li><a href="{{ route('search') }}">All products</a></li>
                    @foreach ($xtFootCats as $item)
                        <li><a href="{{ route('search') }}?category={{ $item->slug }}">{{ $item->name }}</a></li>
                    @endforeach
                    @foreach ($xtFootFeatured as $item)
                        <li><a href="{{ route('front.product.details', $item->slug) }}">{{ $item->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h6>Help</h6>
                <ul>
                    <li><a href="{{ route('front.about') }}">About us</a></li>
                    <li><a href="{{ route('front.contact') }}">Contact us</a></li>
                    <li><a href="{{ route('front.faq') }}">FAQ</a></li>
                    <li><a href="{{ route('front.privacy') }}">Privacy policy</a></li>
                    <li><a href="{{ route('front.terms_conditions') }}">Terms of service</a></li>
                </ul>
            </div>

            <div>
                <h6>Get in touch</h6>
                <ul class="xt-footer__contact">
                    @if (getPhoneNumber())
                        <li><i class="fas fa-phone"></i><a href="tel:{{ getPhoneNumber() }}">{{ getPhoneNumber() }}</a></li>
                    @endif
                    @if (getEmail())
                        <li><i class="fas fa-envelope"></i><a href="mailto:{{ getEmail() }}">{{ getEmail() }}</a></li>
                    @endif
                    @if (getAddress())
                        <li><i class="fas fa-map-marker-alt"></i><span>{{ getAddress() }}</span></li>
                    @endif
                    @if (getOpeningHours())
                        <li><i class="far fa-clock"></i><span>{{ getOpeningHours() }}</span></li>
                    @endif
                </ul>
                @if ($xtFbHasContent)
                    <div class="xt-footer__fb">{!! $xtFb !!}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="xt-footer__bottom">
        <div class="xt-wrap">
            <p>{!! getFooterText() ?: '&copy; ' . date('Y') . ' ' . e(getSiteTitle()) !!}</p>
            {{-- Khalti, eSewa and ConnectIPS hidden for now --}}
            <p>Secure payments with Fonepay and cash on delivery</p>
        </div>
    </div>
</footer>
