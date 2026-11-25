@if ($wishlists->count())
@foreach ($wishlists as $list)
<tr>
    <td class="text-center">
        <a onclick="removeFromWishlist({{$list->product_id}})" class="text-gray-32 font-size-26" style="cursor: pointer !important">×</a>
    </td>
    <td class="d-none d-md-table-cell">
        <a href="{{ route('front.product.details', $list->product->slug) }}"><img
                class="img-fluid max-width-100 p-1 border border-color-1"
                src="{{ asset('uploads/products/' . $list->product->image) }}" alt="{{$list->product->name }}"></a>
    </td>

    <td data-title="Product">
        <a href="{{ route('front.product.details', $list->product->slug) }}" class="text-gray-90">{{$list->product->name
            }}</a>
    </td>

    <td data-title="Unit Price">
        <span class="">{{ getProductPrice($list->product_id) }}</span>
    </td>

    <td data-title="Stock Status">
        @if (getProductQuantity($list->product_id) > 0)
        <span>In stock</span>
        @else
        <span>Not In stock</span>

        @endif
    </td>

    <td>
        <button type="button" onclick="addToCart({{$list->product_id}});remove({{$list->product_id}});"
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