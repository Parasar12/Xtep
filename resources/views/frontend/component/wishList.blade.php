@if (productInWishlist($product->id))
<a onclick="removeFromWishlist({{ $product->id }})" aria-label="Remove From Wishlist" class="text-gray-6 font-size-13 ">
    <i class="fas fa-heart mr-1 font-size-15"></i>
    Remove from Wishlist
</a>
@else
<a onclick="addToWishlist({{ $product->id }})" aria-label="Add To Wishlist" class="text-gray-6 font-size-13 ">
    <i class="far fa-heart mr-1 font-size-15"></i>
    Add
    to Wishlist
</a>
@endif