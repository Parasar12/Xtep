<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model{

	use HasFactory;

	protected $table = 'fcm_token';
  public $timestamps = false;

	protected $fillable = ['user_id', 'fcm_token', 'created_date'];
}

