<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'category_id',
        'product_code',
        'product_name',
        'description',
        'size',
        'color',
        'available_sizes',
        'available_colors',
        'bust',
        'waist',
        'hips',
        'length',
        'rental_price',
        'deposit',
        'stock',
        'status',
        'is_featured',
        'is_popular',
        'is_new',
        'views_count',
        'rental_count',
    ];

    protected function casts(): array
    {
        return [
            'rental_price' => 'decimal:2',
            'deposit' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
                    ->orderBy('is_main', 'desc')
                    ->latest('image_id');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
                    ->where('is_main', true)
                    ->latest('image_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id')
                    ->where('status', 'published')
                    ->latest();
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?: 5, 1);
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getMainImageUrlAttribute()
    {
        $allImages = $this->images;
        
        // Try mainImage first if valid
        $main = $this->mainImage;
        if ($main && !empty($main->image_path)) {
            $path = $main->image_path;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }
            if (file_exists(public_path('storage/' . $path)) || file_exists(storage_path('app/public/' . $path))) {
                return asset('storage/' . $path);
            }
        }

        // Search for any other valid image in the relation
        foreach ($allImages as $img) {
            if (!empty($img->image_path)) {
                $path = $img->image_path;
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }
                if (file_exists(public_path('storage/' . $path)) || file_exists(storage_path('app/public/' . $path))) {
                    return asset('storage/' . $path);
                }
            }
        }

        return 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&auto=format&fit=crop&q=80';
    }

    public function getSizesListAttribute()
    {
        if (!empty($this->available_sizes)) {
            return array_map('trim', explode(',', $this->available_sizes));
        }
        return !empty($this->size) ? [$this->size] : ['S', 'M', 'L'];
    }

    public function getColorsListAttribute()
    {
        if (!empty($this->available_colors)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->available_colors))));
        }
        if (!empty($this->color)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->color))));
        }
        return ['Classic Burgundy'];
    }

    public function getColorsTextAttribute()
    {
        if (!empty($this->available_colors)) {
            return $this->available_colors;
        }
        return $this->color ?: 'ตามแบบ';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'available' => 'ว่าง (พร้อมเช่า)',
            'rented' => 'เช่าอยู่',
            'maintenance' => 'ซ่อม / ปรับปรุง',
            'inactive' => 'ปิดใช้งาน',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'available' => 'badge-success',
            'rented' => 'badge-warning',
            'maintenance' => 'badge-danger',
            'inactive' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
