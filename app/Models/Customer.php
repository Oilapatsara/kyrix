<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    /**
     * ตรงกับคอลัมน์จริงในตาราง `customers` ปัจจุบัน
     * (ไม่มี user_id ในฐานข้อมูลจริงตอนนี้ — ถ้ามีการ migrate เพิ่มคอลัมน์นี้แล้ว
     * ค่อยเติม 'user_id' กลับเข้ามาในลิสต์นี้)
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id', 'customer_id');
    }

    public function getNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $name !== '' ? $name : 'ลูกค้าไม่ระบุชื่อ';
    }
}