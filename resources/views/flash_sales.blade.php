@foreach($flashSales as $flashSale)
@php $checkProducts = checkIfFlashSaleHasUnexpiredProduct($flashSale->id); @endphp

@if($checkProducts)
<div class="row deals-wrapper appear-animate mb-8">
  <div class="col-lg-12 mb-4">
    <div class="single-product h-100 br-sm">
      <h4 class="title-sm title-underline font-weight-bolder ls-normal">
        {{$flashSale->name}}

        @php $today = \Carbon\Carbon::now();
        $endDate = \Carbon\Carbon::parse($flashSale->end_date);
        $startDate = \Carbon\Carbon::parse($flashSale->start_date);
        $year = \Carbon\Carbon::parse($flashSale->end_date)->format('Y');
        $month = \Carbon\Carbon::parse($flashSale->end_date)->format('m');
        $day = \Carbon\Carbon::parse($flashSale->end_date)->format('d');
        $diff = $endDate->diffInDays($today);
        @endphp

        <a href="javascript:" class="btn btn-sm btn-primary btn-outline light ml-5">
          Offer Ends In:
          <div class="product-countdown countdown-compact" data-until="{{$year}}, {{$month}}, {{$day}}" data-compact="true">
            {{$diff}} days, 11: 59: 59
          </div>
        </a>

        <a href="{{url('flash-sales/'.$flashSale->id)}}" class="btn btn-sm btn-primary btn-outline light ml-3">
          View More
        </a>
      </h4>
      <div class="swiper">
        <div class="swiper-container swiper-theme nav-top swiper-nav-lg" data-swiper-options="{
          'spaceBetween': 20,
          'slidesPerView': 1,
          'loop': true,
          'autoplay': true,
          'speed': 1000,
          'breakpoints': {
            '767': {
              'slidesPerView': 2,
              'slidesPerGroup': 1
            }
          }
        }">
        <div class="swiper-wrapper row cols-1 gutter-no fixed-cols">
          @php
          $currentDate = date('Y-m-d');
          $currentDate = date('Y-m-d', strtotime($currentDate));
          @endphp

          @foreach($flashSale->flashSaleProducts as $fp)
          
          <div class="swiper-slide">
            <div class="product product-single row">
              <div class="col-md-6">
                <div
                class="product-gallery product-gallery-sticky product-gallery-vertical">
                <div class="swiper-container product-single-swiper swiper-theme nav-inner">
                  <div class="swiper-wrapper row cols-1 gutter-no">

                    @php $gallery = getProductGalleryImage($fp->product_id); @endphp

                    @foreach($gallery as $g)
                    <div class="swiper-slide">
                      <figure class="product-image">
                        <img src="{{ $g }}"
                        data-zoom-image="{{ $g }}"
                        alt="Product Image" width="800" height="900">
                      </figure>
                    </div>
                    @endforeach
                  </div>

                  <button class="swiper-button-next"></button>
                  <button class="swiper-button-prev"></button>
                  <div class="product-label-group">
                    <label class="product-label label-discount">25%
                    Off</label>
                  </div>
                </div>
                <div class="product-thumbs-wrap swiper-container"
                data-swiper-options="{
                  'direction': 'vertical',
                  'breakpoints': {
                    '0': {
                      'direction': 'horizontal',
                      'slidesPerView': 4
                    },
                    '992': {
                      'direction': 'vertical',
                      'slidesPerView': 'auto'
                    }
                  }
                }">
                <div class="product-thumbs swiper-wrapper row cols-lg-1 cols-4 gutter-sm">    
                  @foreach($gallery as $g)
                  <div class="product-thumb swiper-slide">
                    <img src="{{ $g }}" alt="Product thumb"
                    width="60" height="68" />
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="product-details scrollable">
              <h2 class="product-title mb-1">
                <a href="{{url('product/'.$fp->product->slug)}}">{{$fp->product->name}}</a>
              </h2>

              <hr class="product-divider">

              <div class="product-price">
                <ins class="new-price ls-50">
                  Rs. {{getProductPrice($fp->product_id)}}
                </ins>
              </div>

              <div class="ratings-container">
                <div class="ratings-full">
                  <span class="ratings" style="width: 80%;"></span>
                  <span class="tooltiptext tooltip-top"></span>
                </div>
                <a href="#" class="rating-reviews">(3 Reviews)</a>
              </div>

              @foreach($fp->product->productAttributes as $pa)
              @php $attribute = \App\Models\Attribute::find($pa->attribute_id)->name;
              $attributeValues = explode(" ",$pa->value);
              @endphp
              <div class="product-form product-variation-form product-size-swatch mb-3">
                <label class="mb-1">{{$attribute}}:</label>
                <div class="flex-wrap d-flex align-items-center product-variations">
                  @foreach($attributeValues as $value)
                  <a href="#" class="size {{$attribute}}">{{$value}}</a>
                  @endforeach
                </div>
                <a href="#" class="product-variation-clean">Clean All</a>
              </div>
              @endforeach

              <div class="product-variation-price">
                <span></span>
              </div>

              <form method="post" action="{{route('cart.store')}}"
              enctype="multipart/form-data" id="store_flash_cart">
              @csrf
              <div class="product-form pt-4">
                <div class="product-qty-form mb-2 mr-2">
                  <div class="input-group">
                    <input class="form-control" type="number" name="quantity"
                    value="1" readonly required>

                    <div class="product_quantity">
                      <span class="increaseQuantity quantity-plus w-icon-plus"></span>
                      <span class="decreaseQuantity quantity-minus w-icon-minus"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="product_id"
                    value="{{$fp->product->id}}">

                  </div>
                  <div class="form-group">
                    <input type="hidden" class="form-control" name="available"
                    value="{{getProductQuantity($fp->product->id)}}">

                  </div>
                </div>

                <button class="btn btn-primary btn-cart">
                  <i class="w-icon-cart"></i>
                  <span>Add to Cart</span>
                </button>
              </div>
            </form>

            {{-- <div class="social-links-wrapper mt-1">
              <span class="divider d-xs-show"></span>
              <div class="product-link-wrapper d-flex">
                <a class="btn-product-icon btn-wishlist w-icon-heart"
                @if(auth()->user())@if(productInWishlist($fp->product->id))
                style="color: red;"
                onclick="removeFromWishlist({{$fp->product->id}})" @else
                onclick="addToWishlist({{$fp->product->id}})" @endif @endif></a>
              </div>
            </div> --}}
          </div>
        </div>
      </div>
    </div>

    @endforeach
  </div>
  <button class="swiper-button-prev"></button>
  <button class="swiper-button-next"></button>
</div>
</div>
</div>
</div>
</div>
@endif
@endforeach
