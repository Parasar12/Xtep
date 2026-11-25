<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceProblem extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'device_problem_master';
    protected $guarded = [];
}
