<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $primaryKey = 'permission_id';
    public $timestamps = false;

    protected $table = 'user_permission';

    protected $fillable = ['permission_id', 'user_id','menu_id','created_by','created_date'];
}
