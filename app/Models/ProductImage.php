<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $primaryKey = 'image_id';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_path',
        'is_main',
        'color_name',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id',
            'product_id'
        );
    }

    /**
     * คืนค่า URL ของรูปสินค้า
     */
    public function getUrlAttribute()
    {
        if (empty($this->image_path)) {
            return $this->defaultImage();
        }

        $path = trim($this->image_path);

        // ถ้าเป็น URL อยู่แล้ว
        if (
            str_starts_with($path, 'https://') ||
            str_starts_with($path, 'http://')
        ) {
            // เปลี่ยน http เป็น https
            if (str_starts_with($path, 'http://')) {
                $path = 'https://' . substr($path, 7);
            }

            return $path;
        }

        // ถ้าเป็น path ของ storage
        return asset(
            'storage/' . ltrim($path, '/')
        );
    }

    /**
     * รูปสำรองกรณีไม่มีรูป
     */
    public function defaultImage()
    {
        return 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?auto=format&fit=crop&w=800&q=80';
    }
}