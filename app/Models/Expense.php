<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $guarded = [];

    public $timestamps = false;

    public function payment(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }
}
