<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceCondition extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'device_condition_master';
    protected $guarded = [];
}
