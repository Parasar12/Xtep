<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function categories() {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    //each category might have multiple children
    public function children() {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }
}
