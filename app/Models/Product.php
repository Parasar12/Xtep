<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

protected $guarded = [];
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function gallary()
    {
        return $this->hasMany(ProductGallary::class);
    }

    public function productAttributes()
    {

        return $this->hasMany(ProductAttribute::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function productMasters()
    {
        return $this->hasMany(ProductMaster::class)->latest();
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
