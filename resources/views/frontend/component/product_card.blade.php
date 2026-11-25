{{-- Product card used on the homepage. Matches the shop listing card (.xt-card).
     Expects: $product (App\Models\Product). Optional: $badge (string). --}}
@php
    try {
        $xtPrice = (float) getProductPrice($product->id);
    } catch (\Throwable $e) {
        $xtPrice = 0;
    }
    $xtRegular = (float) \App\Models\ProductMaster::where('product_id', $product->id)->where('is_active', 1)->value('price');
    $xtOnSale = $xtRegular > 0 && $xtPrice > 0 && $xtPrice < $xtRegular;
    $xtBadge = $xtOnSale ? 'Sale' : ($badge ?? null);
@endphp
<a href="{{ route('front.product.details', $product->slug) }}" class="xt-card">
    <div class="xt-card__img">
        @if ($xtBadge)
            <span class="xt-card__badge {{ $xtOnSale ? 'xt-card__badge--red' : '' }}">{{ $xtBadge }}</span>
        @endif
        <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy"
            onerror="this.onerror=null;this.style.opacity=0">
    </div>
    <div class="xt-card__body">
        <div class="xt-card__cat">{{ $product->category->name ?? '' }}</div>
        <h5 class="xt-card__title">{{ $product->name }}</h5>
        <div class="xt-card__price">
            Rs. {{ number_format($xtPrice) }}
            @if ($xtOnSale)
                <del>Rs. {{ number_format($xtRegular) }}</del>
            @endif
        </div>
    </div>
</a>
