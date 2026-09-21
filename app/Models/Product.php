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

            'views_count' => 'integer',
            'rental_count' => 'integer',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'category_id'
        );
    }


    /**
     * รูปภาพทั้งหมดของสินค้า
     *
     * product_images.product_id
     * ต้องตรงกับ
     * products.product_id
     */
    public function images()
    {
        return $this->hasMany(
            ProductImage::class,
            'product_id',
            'product_id'
        )
        ->orderByDesc('is_main')
        ->orderByDesc('image_id');
    }


    /**
     * รูปหลักของสินค้า
     *
     * ใช้เฉพาะรูปที่ is_main = 1
     */
    public function mainImage()
    {
        return $this->hasOne(
            ProductImage::class,
            'product_id',
            'product_id'
        )
        ->where('is_main', 1)
        ->orderByDesc('image_id');
    }


    public function reviews()
    {
        return $this->hasMany(
            Review::class,
            'product_id',
            'product_id'
        )
        ->where('status', 'published')
        ->latest();
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * คะแนนรีวิวเฉลี่ย
     */
    public function getAverageRatingAttribute()
    {
        return round(
            $this->reviews()->avg('rating') ?: 5,
            1
        );
    }


    /**
     * จำนวนรีวิว
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }


    /**
     * URL รูปหลักของสินค้า
     *
     * ลำดับการทำงาน:
     * 1. ใช้ product_images ที่ is_main = 1
     * 2. ถ้าไม่มี ใช้รูปแรกของ product_id เดียวกัน
     * 3. ไม่มีรูป = null
     *
     * ไม่มี Unsplash fallback
     * เพื่อป้องกันการแสดงรูปซ้ำระหว่างสินค้า
     */
    public function getMainImageUrlAttribute()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. รูปหลัก
        |--------------------------------------------------------------------------
        */

        $main = $this->mainImage;

        if (
            $main &&
            !empty($main->image_path)
        ) {
            return $this->buildImageUrl(
                $main->image_path
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 2. ถ้าไม่มีรูปหลัก ใช้รูปแรกของสินค้านั้น
        |--------------------------------------------------------------------------
        */

        $firstImage = $this->images->first();

        if (
            $firstImage &&
            !empty($firstImage->image_path)
        ) {
            return $this->buildImageUrl(
                $firstImage->image_path
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 3. ไม่มีรูป
        |--------------------------------------------------------------------------
        */

        return null;
    }


    /**
     * แปลง image_path เป็น URL
     *
     * รองรับ:
     * - https://...
     * - http://...
     * - //...
     * - storage/...
     * - path รูปใน storage
     */
    private function buildImageUrl($imagePath)
    {
        $imagePath = trim(
            (string) $imagePath
        );

        if ($imagePath === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | URL ภายนอก เช่น GitHub Raw
        |--------------------------------------------------------------------------
        */

        if (
            filter_var(
                $imagePath,
                FILTER_VALIDATE_URL
            )
        ) {
            return $imagePath;
        }


        /*
        |--------------------------------------------------------------------------
        | Protocol-relative URL
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $imagePath,
                '//'
            )
        ) {
            return $imagePath;
        }


        /*
        |--------------------------------------------------------------------------
        | Path ภายในระบบ
        |--------------------------------------------------------------------------
        */

        $cleanPath = ltrim(
            $imagePath,
            '/'
        );


        /*
        | ถ้ามี storage/ อยู่แล้ว
        */

        if (
            str_starts_with(
                $cleanPath,
                'storage/'
            )
        ) {
            return asset(
                $cleanPath
            );
        }


        /*
        | Path ทั่วไป เช่น products/xxx.jpg
        */

        return asset(
            'storage/' . $cleanPath
        );
    }


    /**
     * รายการไซซ์ทั้งหมด
     */
    public function getSizesListAttribute()
    {
        if (
            !empty(
                $this->available_sizes
            )
        ) {
            return array_values(
                array_filter(
                    array_map(
                        'trim',
                        explode(
                            ',',
                            $this->available_sizes
                        )
                    )
                )
            );
        }


        if (
            !empty(
                $this->size
            )
        ) {
            return [
                $this->size
            ];
        }


        return [
            'S',
            'M',
            'L'
        ];
    }


    /**
     * รายการสีทั้งหมด
     */
    public function getColorsListAttribute()
    {
        if (
            !empty(
                $this->available_colors
            )
        ) {
            return array_values(
                array_filter(
                    array_map(
                        'trim',
                        explode(
                            ',',
                            $this->available_colors
                        )
                    )
                )
            );
        }


        if (
            !empty(
                $this->color
            )
        ) {
            return array_values(
                array_filter(
                    array_map(
                        'trim',
                        explode(
                            ',',
                            $this->color
                        )
                    )
                )
            );
        }


        return [
            'Classic Burgundy'
        ];
    }


    /**
     * ข้อความสี
     */
    public function getColorsTextAttribute()
    {
        if (
            !empty(
                $this->available_colors
            )
        ) {
            return $this->available_colors;
        }

        return $this->color ?: 'ตามแบบ';
    }


    /**
     * ชื่อสถานะสินค้า
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {

            'available' =>
                'ว่าง (พร้อมเช่า)',

            'rented' =>
                'เช่าอยู่',

            'maintenance' =>
                'ซ่อม / ปรับปรุง',

            'inactive' =>
                'ปิดใช้งาน',

            default =>
                $this->status,
        };
    }


    /**
     * CSS class ของสถานะสินค้า
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {

            'available' =>
                'badge-success',

            'rented' =>
                'badge-warning',

            'maintenance' =>
                'badge-danger',

            'inactive' =>
                'badge-secondary',

            default =>
                'badge-secondary',
        };
    }
}