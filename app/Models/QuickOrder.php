<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickOrder extends Model
{
    use HasFactory;


    protected $guarded = [];
    protected $table = 'quick_orders';
    public  $timestamps =false;


    public function productAttribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class,'attribute');
    }
}
