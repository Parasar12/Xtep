<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAccessory extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'device_accessories_master';
    protected $guarded = [];
}
