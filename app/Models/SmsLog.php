<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'sms_logs';
    protected $guarded = [];
    protected $primaryKey = 'log_id';

}
