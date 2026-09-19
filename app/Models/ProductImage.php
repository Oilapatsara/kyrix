<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'image_id';
    public $timestamps = false;
    protected $fillable = ['product_id', 'image_path', 'is_main', 'color_name', 'created_at'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function getUrlAttribute()
    {
        if (empty($this->image_path)) {
            return 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80';
        }
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }
}
