<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rental extends Model
{
    protected $table = 'rentals';

    protected $primaryKey = 'rental_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'rental_code',
        'customer_id',
        'rental_date',
        'start_date',
        'end_date',
        'total_amount',
        'deposit_amount',
        'service_type',
        'service_fee',
        'delivery_method',
        'delivery_address',
        'recipient_phone',
        'tracking_number',
        'return_tracking_no',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'rental_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'service_fee' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'customer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rental Details
    |--------------------------------------------------------------------------
    */

    public function details(): HasMany
    {
        return $this->hasMany(
            RentalDetail::class,
            'rental_id',
            'rental_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'rental_id',
            'rental_id'
        )->latest('payment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Latest Payment
    |--------------------------------------------------------------------------
    |
    | สำคัญ:
    | ตาราง payments ไม่มี id
    | Primary Key คือ payment_id
    |
    | ระบุ payment_id โดยตรง เพื่อป้องกัน Laravel
    | สร้าง SQL ที่เรียก payments.id
    |
    */

    public function latestPayment(): HasOne
    {
        return $this->hasOne(
            Payment::class,
            'rental_id',
            'rental_id'
        )->latestOfMany('payment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'rental_id',
            'rental_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Customer Full Name
    |--------------------------------------------------------------------------
    */

    public function getCustomerFullNameAttribute(): string
    {
        if ($this->relationLoaded('customer') && $this->customer) {
            $firstName = trim($this->customer->first_name ?? '');
            $lastName = trim($this->customer->last_name ?? '');

            $fullName = trim($firstName . ' ' . $lastName);

            if ($fullName !== '') {
                return $fullName;
            }
        }

        return $this->customer_name ?? 'ไม่ระบุชื่อ';
    }

    /*
    |--------------------------------------------------------------------------
    | Formatted Rental Code
    |--------------------------------------------------------------------------
    |
    | ตัวอย่าง:
    | #R00001
    |
    */

    public function getFormattedCodeAttribute(): string
    {
        if (
            !empty($this->rental_code) &&
            str_starts_with($this->rental_code, '#R')
        ) {
            return $this->rental_code;
        }

        return '#R' . str_pad(
            $this->rental_id,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status Label
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending',
            'pending_payment',
            'pending_verification'
                => 'รอดำเนินการ',

            'confirmed',
            'ready_pickup'
                => 'ยืนยันการเช่า',

            'renting',
            'pending_return'
                => 'กำลังเช่า',

            'returned',
            'completed'
                => 'คืนชุดแล้ว',

            'cancelled'
                => 'ยกเลิก',

            default
                => $this->status ?? '-',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Status Badge Class
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending',
            'pending_payment',
            'pending_verification'
                => 'badge-warning',

            'confirmed',
            'ready_pickup'
                => 'badge-primary',

            'renting',
            'pending_return'
                => 'badge-info',

            'returned',
            'completed'
                => 'badge-success',

            'cancelled'
                => 'badge-danger',

            default
                => 'badge-secondary',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Status Label
    |--------------------------------------------------------------------------
    */

    public function getPaymentStatusLabelAttribute(): string
    {
        $lastPayment = $this->latestPayment;

        if (!$lastPayment) {
            return 'ยังไม่ชำระ';
        }

        return match ($lastPayment->status) {
            'pending'
                => 'รอตรวจสอบ',

            'approved'
                => 'อนุมัติแล้ว',

            'rejected'
                => 'ถูกปฏิเสธ',

            default
                => $lastPayment->status ?? '-',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Badge Class
    |--------------------------------------------------------------------------
    */

    public function getPaymentBadgeClassAttribute(): string
    {
        $lastPayment = $this->latestPayment;

        if (!$lastPayment) {
            return 'badge-secondary';
        }

        return match ($lastPayment->status) {
            'pending'
                => 'badge-warning',

            'approved'
                => 'badge-success',

            'rejected'
                => 'badge-danger',

            default
                => 'badge-secondary',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Grand Total
    |--------------------------------------------------------------------------
    */

    public function getGrandTotalAttribute(): float
    {
        return (float) $this->total_amount
            + (float) $this->deposit_amount
            + (float) ($this->service_fee ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Paid Amount
    |--------------------------------------------------------------------------
    |
    | ตาราง payments ใช้ payment_amount
    | ไม่ใช่ amount
    |
    */

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()
            ->where('status', 'approved')
            ->sum('payment_amount');
    }
}