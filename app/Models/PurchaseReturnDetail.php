<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnDetail extends Model
{
    protected $table = "purchase_return_details";
    protected $guarded = [];
    public $timestamps = false;

    public function product_attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

}
