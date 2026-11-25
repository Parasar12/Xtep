<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePaymentTrack extends Model
{
    protected $table = "sales_payments";
    protected $guarded = [];
    public $timestamps = false;

    public function method(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'sale_entry_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }

}
