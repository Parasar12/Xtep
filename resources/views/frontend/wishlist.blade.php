@extends('frontend.layouts.master')
@section('meta')
<meta name="description" content="">
<meta name="keywords" content="">

<title>{{ getSiteTitle() }} - Wishlist</title>
@endsection
@section('content')

<div class="bg-gray-13 bg-md-transparent">
    <div class="container">
        <!-- breadcrumb -->
        <div class="my-md-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3 flex-nowrap flex-xl-wrap overflow-auto overflow-xl-visble">
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1"><a
                            href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item flex-shrink-0 flex-xl-shrink-1 active" aria-current="page">Wishlist</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->
    </div>
</div>
<div class="container">
    <div class="my-6">
        <h1 class="text-center">My Wishlist</h1>
    </div>
    <div class="mb-16 wishlist-table">

        <div class="table-responsive">
            <table class="table" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-remove">&nbsp;</th>
                        <th class="product-thumbnail">&nbsp;</th>
                        <th class="product-name">Product</th>
                        <th class="product-price">Unit Price</th>
                        {{-- <th class="product-Stock w-lg-15">Stock Status</th> --}}
                        <th class="product-subtotal min-width-200-md-lg">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="wishlistTableBody">
                    @if ($wishlists->count())
                    @foreach ($wishlists as $list)
                    {{-- @dd($list) --}}
                    <tr>
                        <td class="text-center">
                            <a onclick="removeFromWishlist({{$list->product_id}}); location.reload();"  class="text-gray-32 font-size-26" style="cursor: pointer !important">×</a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <a href="{{ route('front.product.details', $list->product->slug) }}"><img
                                    class="img-fluid max-width-100 p-1 border border-color-1"
                                    src="{{ asset('uploads/products/' . $list->product->image) }}"
                                    alt="{{$list->product->name }}"></a>
                        </td>

                        <td data-title="Product">
                            <a href="{{ route('front.product.details', $list->product->slug) }}"
                                class="text-gray-90">{{$list->product->name }}</a>
                        </td>

                        <td data-title="Unit Price">
                            <span class="">Rs. {{ getProductPrice($list->product_id) }}</span>
                        </td>

                        {{-- <td data-title="Stock Status">
                            @if (getProductQuantity($list->product_id) > 0)
                            <span>In stock</span>
                            @else
                            <span>Not In stock</span>

                            @endif
                        </td> --}}

                        <td>
                            <button type="button"
                                onclick="addToCart({{$list->product_id}});remove({{$list->product_id}});"
                                class="btn btn-soft-secondary mb-3 mb-md-0 font-weight-normal px-5 px-md-4 px-lg-5 w-100 w-md-auto">Add
                                to Cart</button>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="text-center" colspan="6">No Wishlist Found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection

@section('script')
<script>
    function remove(id) {
            $.ajax({
                url: "{{ route('wishlists.remove.list')  }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    'id': id
                },
                success: function (response) {
                    if (response.response) {
                        toastr.success(response.msg);
                        $('#wishlistTableBody').html('')
                        $('#wishlistTableBody').html(response.result)
                    } else {
                        toastr.error(response.msg)
                        location.reload()
                    }
                }
            })
        }
</script>
@endsection