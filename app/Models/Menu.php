<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model{
    use HasFactory;

    protected $table = 'menu_master';

    protected $fillable = ['menu_code','menu_name','menu_index','menu_type','pre_menu_id','is_active','icon_class','route','created_by'];
}
