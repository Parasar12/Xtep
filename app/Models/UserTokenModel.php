<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTokenModel extends Model{

    use HasFactory;

    public $timestamps = false;

    protected $table = 'user_token';

    protected $fillable = ['user_id','token'];
}
