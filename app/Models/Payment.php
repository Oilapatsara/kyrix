<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    | ตาราง payments ใช้ payment_id ไม่ใช่ id
    */
    protected $primaryKey = 'payment_id';

    public $incrementing = true;

    protected $keyType = 'int';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'rental_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'slip_image',
        'status',
        'note',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'payment_amount' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship : Rental
    |--------------------------------------------------------------------------
    */
    public function rental(): BelongsTo
    {
        return $this->belongsTo(
            Rental::class,
            'rental_id',
            'rental_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Slip URL
    |--------------------------------------------------------------------------
    */
    public function getSlipUrlAttribute(): ?string
    {
        if (empty($this->slip_image)) {
            return null;
        }

        /*
        | กรณี 1: ถ้าเป็น URL เต็มภายนอก (http:// หรือ https://)
        */
        if (
            str_starts_with($this->slip_image, 'http://') ||
            str_starts_with($this->slip_image, 'https://')
        ) {
            return $this->slip_image;
        }

        /*
        | กรณี 2: ถ้าเป็น Path ไฟล์ภายในโปรเจกต์ (เช่น uploads/slips/...)
        | ตัดคำว่า storage/ ออก เพื่อให้ดึงจากโฟลเดอร์ public ตรงๆ
        */
        return asset(ltrim($this->slip_image, '/'));
    }
}