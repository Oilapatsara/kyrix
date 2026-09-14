<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name', 'description', 'status'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}
