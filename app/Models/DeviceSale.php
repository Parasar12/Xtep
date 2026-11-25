<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSale extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'device_sales_master';
    protected $guarded = [];

    public function deviceCondition(){
        return $this->belongsTo(DeviceCondition::class,'condtion_id','id');
    }
}
