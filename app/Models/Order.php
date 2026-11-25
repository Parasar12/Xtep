<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'orders';

    protected $primaryKey = 'id';

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function orderBy()
    {
        return $this->belongsTo(User::class, 'order_by');
    }

    public function deliveryStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'delivery_status');
    }

    public function addresses()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function profit_loss()
    {
        return $this->hasOne(SaleProfitLoss::class, 'order_id');
    }

    public function order_mode()
    {
        return $this->belongsTo(OrderMode::class, 'mode_type');
    }

    public function sales_by()
    {
        return $this->belongsTo(User::class, 'order_by');
    }
}
