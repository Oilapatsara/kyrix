<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
    ];

    /**
     * ซ่อนฟิลด์รหัสผ่านไม่ให้แสดงผลออกมาเมื่อถูกแปลงเป็น Array หรือ JSON
     */
    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Accessor สำหรับดึงชื่อ-นามสกุลรวมกัน (First Name + Last Name)
     */
    public function getFullNameAttribute()
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');
        $fullName = trim($firstName . ' ' . $lastName);

        return $fullName !== '' ? $fullName : 'ไม่ระบุชื่อ';
    }

    /**
     * ความสัมพันธ์: ประวัติการเช่าทั้งหมดของลูกค้า
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id')->latest();
    }

    /**
     * ความสัมพันธ์: รีวิวทั้งหมดของลูกค้า
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id', 'customer_id')->latest();
    }
}