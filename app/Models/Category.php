<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'category_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'category_name',
        'description',
        'status',
    ];

    /**
     * หมวดหมู่มีชุดหลายชุด
     */
    public function products()
    {
        return $this->hasMany(
            Product::class,
            'category_id',
            'category_id'
        );
    }

    /**
     * Scope สำหรับหมวดหมู่ที่เปิดใช้งาน
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}