<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReturnDetail extends Model
{
    protected $table = "sales_return_details";
    protected $guarded = [];
    public $timestamps = false;

    public function sales_return(): BelongsTo
    {
        return $this->belongsTo(SaleReturn::class, 'sales_return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
