<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogistiPaymentTrack extends Model
{
    protected $table = "logistic_payment_track";
    protected $guarded = [];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function method(){
        return $this->belongsTo(PaymentMethod::class,'payment_method_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }

}
