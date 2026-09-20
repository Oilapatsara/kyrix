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
        'discount_amount',
        'discount_reason',
        'deposit_amount',
        'service_type',
        'service_fee',
        'delivery_method',
        'delivery_address',
        'recipient_phone',

        // ข้อมูลการจัดส่ง
        'tracking_number',
        'shipping_carrier',
        'shipping_status',
        'tracking_url',
        'shipped_at',
        'estimated_delivery_at',

        // กำหนดคืนชุด
        'return_due_at',

        // การส่งคืนสินค้า
        'return_tracking_no',

        'status',
        'note',
        'condition_status',
        'deposit_status',
        'deposit_refund_amount',
        'damage_note',
        'damage_image',
        'refund_slip',
        'inspected_at',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
        'deposit_amount' => 'float',
        'service_fee' => 'float',
        'deposit_refund_amount' => 'float',
        'inspected_at' => 'datetime',

        // ข้อมูลวันที่และเวลาการจัดส่ง
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',

        // วันและเวลาที่ต้องคืนชุด
        'return_due_at' => 'datetime',
    ];

    /**
     * Accessor: Grand Total
     * Net rental + deposit + service fee
     */
    public function getGrandTotalAttribute(): float
    {
        $netRental = max(
            0.0,
            (float) ($this->total_amount ?? 0)
                - (float) ($this->discount_amount ?? 0)
        );

        return $netRental
            + (float) ($this->deposit_amount ?? 0)
            + (float) ($this->service_fee ?? 0);
    }

    /**
     * Accessor: Net Rental Amount
     * ค่าเช่าชุดหลังหักส่วนลด
     */
    public function getNetRentalAmountAttribute(): float
    {
        return max(
            0.0,
            (float) ($this->total_amount ?? 0)
                - (float) ($this->discount_amount ?? 0)
        );
    }

    /**
     * Accessor: Formatted Rental Code
     */
    public function getFormattedCodeAttribute(): string
    {
        return $this->rental_code
            ?: (
                'KR-' .
                date(
                    'Ym',
                    strtotime($this->created_at ?? now())
                ) .
                '-' .
                str_pad(
                    $this->rental_id,
                    4,
                    '0',
                    STR_PAD_LEFT
                )
            );
    }

    /**
     * Accessor: Damage Image URL
     */
    public function getDamageImageUrlAttribute(): ?string
    {
        if (!$this->damage_image) {
            return null;
        }

        if (str_starts_with($this->damage_image, 'http')) {
            return $this->damage_image;
        }

        return asset($this->damage_image);
    }

    /**
     * Accessor: Refund Slip URL
     */
    public function getRefundSlipUrlAttribute(): ?string
    {
        if (!$this->refund_slip) {
            return null;
        }

        if (str_starts_with($this->refund_slip, 'http')) {
            return $this->refund_slip;
        }

        return asset($this->refund_slip);
    }

    /**
     * Accessor: Step Index for Timeline
     */
    public function getStepIndexAttribute(): int
    {
        return match ($this->status) {
            'pending',
            'pending_payment' => 1,

            'pending_verification' => 2,

            'confirmed' => 3,

            'ready_pickup' => 4,

            'renting',
            'pending_return' => 5,

            'returned' => 6,

            'completed' => 7,

            default => 1,
        };
    }

    /**
     * Accessor: Status Label Thai
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending',
            'pending_payment' => 'รอชำระเงิน',

            'pending_verification' => 'รอตรวจสอบสลิป',

            'confirmed' => 'ยืนยันการเช่า',

            'ready_pickup' => 'รอรับชุด',

            'renting' => 'กำลังเช่า',

            'pending_return' => 'รอตรวจรับคืน',

            'returned' => 'คืนชุดแล้ว',

            'completed' => 'เสร็จสิ้นรายการ',

            'cancelled' => 'ยกเลิกรายการ',

            default => ucfirst($this->status),
        };
    }

    /**
     * Accessor: Status Badge Class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending',
            'pending_payment' => 'badge-pending',

            'pending_verification' => 'badge-warning',

            'confirmed',
            'ready_pickup' => 'badge-info',

            'renting' => 'badge-primary',

            'pending_return' => 'badge-warning',

            'returned',
            'completed' => 'badge-success',

            'cancelled' => 'badge-danger',

            default => 'badge-secondary',
        };
    }

    /**
     * Accessor: Can Review
     */
    public function getCanReviewAttribute(): bool
    {
        return in_array(
            $this->status,
            ['returned', 'completed'],
            true
        );
    }

    /**
     * ยอดเงินที่ชำระแล้ว
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments
            ->where('status', 'approved')
            ->sum('payment_amount');
    }

    /**
     * สถานะการชำระเงิน
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        $payment = $this->latestPayment;

        if (!$payment) {
            return 'ยังไม่ชำระ';
        }

        return match ($payment->status) {
            'approved' => 'ชำระแล้ว',
            'pending' => 'รอตรวจสอบ',
            'rejected' => 'สลิปถูกปฏิเสธ',
            default => ucfirst($payment->status),
        };
    }

    /**
     * Class ของ Badge การชำระเงิน
     */
    public function getPaymentBadgeClassAttribute(): string
    {
        $payment = $this->latestPayment;

        if (!$payment) {
            return 'badge-danger';
        }

        return match ($payment->status) {
            'approved' => 'badge-success',
            'pending' => 'badge-warning',
            'rejected' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    /**
     * Accessor: สถานะการจัดส่งภาษาไทย
     */
    public function getShippingStatusLabelAttribute(): string
    {
        return match ($this->shipping_status) {
            'กำลังเตรียมสินค้า' => 'กำลังเตรียมสินค้า',
            'ส่งพัสดุแล้ว' => 'ส่งพัสดุแล้ว',
            'กำลังขนส่ง' => 'กำลังขนส่ง',
            'กำลังนำจ่าย' => 'กำลังนำจ่าย',
            'จัดส่งสำเร็จ' => 'จัดส่งสำเร็จ',
            default => 'ยังไม่มีข้อมูล',
        };
    }

    /**
     * Accessor: สร้างลิงก์ติดตามพัสดุอัตโนมัติ
     * จากบริษัทขนส่ง + เลขพัสดุ
     */
    public function getTrackingUrlAttribute($value): ?string
    {
        $trackingNumber = trim(
            (string) ($this->tracking_number ?? '')
        );

        if ($trackingNumber === '') {
            return $value ?: null;
        }

        $number = rawurlencode($trackingNumber);

        $carrier = strtolower(
            trim((string) ($this->shipping_carrier ?? ''))
        );

        return match ($carrier) {
            'ไปรษณีย์ไทย',
            'thailand post',
            'thai post' =>
                'https://track.thailandpost.co.th/?trackNumber=' . $number,

            'j&t express',
            'j&t' =>
                'https://www.jtexpress.co.th/service/track?waybillNo=' . $number,

            'flash express',
            'flash' =>
                'https://flashexpress.com/fle/tracking',

            'kex',
            'kerry',
            'kerry express' =>
                'https://th.kex-express.com/en/track-parcel',

            'ninja van',
            'ninjavan' =>
                'https://www.ninjavan.co/en-th/tracking',

            'dhl',
            'dhl express' =>
                'https://www.dhl.com/th-th/home/tracking.html',

            'best express',
            'best' =>
                'https://www.best-inc.co.th/',

            default =>
                $value ?: null,
        };
    }

    /**
     * Accessor: ตรวจสอบว่าคืนชุดเกินกำหนดหรือไม่
     */
    public function getIsReturnOverdueAttribute(): bool
    {
        if (!$this->return_due_at) {
            return false;
        }

        if (
            in_array(
                $this->status,
                ['returned', 'completed', 'cancelled'],
                true
            )
        ) {
            return false;
        }

        return now()->greaterThan($this->return_due_at);
    }

    /**
     * Accessor: วันเวลาที่ต้องคืนชุดแบบอ่านง่าย
     */
    public function getReturnDueLabelAttribute(): ?string
    {
        if (!$this->return_due_at) {
            return null;
        }

        return $this->return_due_at->format('d/m/Y H:i') . ' น.';
    }

    /**
     * rentals.customer_id -> customers.customer_id
     */
    public function customer()
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'customer_id'
        );
    }

    /**
     * rentals.rental_id -> rental_details.rental_id
     */
    public function details()
    {
        return $this->hasMany(
            RentalDetail::class,
            'rental_id',
            'rental_id'
        );
    }

    /**
     * Alias for details
     */
    public function rentalDetails()
    {
        return $this->details();
    }

    /**
     * rentals.rental_id -> payments.rental_id
     */
    public function payments()
    {
        return $this->hasMany(
            Payment::class,
            'rental_id',
            'rental_id'
        );
    }

    /**
     * Latest payment
     */
    public function latestPayment()
    {
        return $this->hasOne(
            Payment::class,
            'rental_id',
            'rental_id'
        )->latestOfMany('payment_id');
    }

    /**
     * rentals.rental_id -> reviews.rental_id
     */
    public function reviews()
    {
        return $this->hasMany(
            Review::class,
            'rental_id',
            'rental_id'
        );
    }
}