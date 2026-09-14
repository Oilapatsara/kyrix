<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(RentalDetail::class, 'rental_id', 'rental_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'rental_id', 'rental_id')->latest();
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'rental_id', 'rental_id')->latestOfMany('payment_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'rental_id', 'rental_id');
    }

    /**
     * Rental Code display format, e.g. #R00001
     */
    public function getFormattedCodeAttribute()
    {
        if (!empty($this->rental_code) && str_starts_with($this->rental_code, '#R')) {
            return $this->rental_code;
        }
        return '#R' . str_pad($this->rental_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Exact 5 statuses from database:
     * pending   -> รอดำเนินการ
     * confirmed -> ยืนยันการเช่า
     * renting   -> กำลังเช่า
     * returned  -> คืนชุดแล้ว
     * cancelled -> ยกเลิก
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending', 'pending_payment', 'pending_verification' => 'รอดำเนินการ',
            'confirmed', 'ready_pickup' => 'ยืนยันการเช่า',
            'renting', 'pending_return' => 'กำลังเช่า',
            'returned', 'completed' => 'คืนชุดแล้ว',
            'cancelled' => 'ยกเลิก',
            default => $this->status,
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending', 'pending_payment', 'pending_verification' => 'badge-warning',
            'confirmed', 'ready_pickup' => 'badge-primary',
            'renting', 'pending_return' => 'badge-info',
            'returned', 'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getPaymentStatusLabelAttribute()
    {
        $lastPayment = $this->latestPayment;
        if (!$lastPayment) {
            return 'ยังไม่ชำระ';
        }
        return match ($lastPayment->status) {
            'pending' => 'รอตรวจสอบ',
            'approved' => 'อนุมัติแล้ว',
            'rejected' => 'ถูกปฏิเสธ',
            default => $lastPayment->status,
        };
    }

    public function getPaymentBadgeClassAttribute()
    {
        $lastPayment = $this->latestPayment;
        if (!$lastPayment) {
            return 'badge-secondary';
        }
        return match ($lastPayment->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getGrandTotalAttribute()
    {
        return $this->total_amount + $this->deposit_amount + ($this->service_fee ?? 0);
    }

    public function getPaidAmountAttribute()
    {
        return (float) $this->payments()->where('status', 'approved')->sum('payment_amount');
    }
}
