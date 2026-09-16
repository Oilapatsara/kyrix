<?php

namespace App\Services;

class PromotionService
{
    public const DISCOUNT_PERCENT = 20;
    public const MIN_ITEMS_FOR_DISCOUNT = 3;
    public const MIN_AMOUNT_FOR_DISCOUNT = 2000.0;

    /**
     * คำนวณส่วนลดโปรโมชั่น:
     * - ลูกค้าเช่าครบ 3 รายการขึ้นไป ทางร้านลดให้ 20%
     * - หรือ ยอดเช่าครบ 2,000 บาทขึ้นไป ทางร้านลดให้ 20%
     *
     * @param int $itemCount จำนวนรายการชุดที่เช่า
     * @param float $rentalTotal ยอดค่าเช่าชุดรวม (ก่อนคิดมัดจำและบริการเสริม)
     * @return array
     */
    public static function calculateDiscount(int $itemCount, float $rentalTotal): array
    {
        $rentalTotal = max(0.0, (float)$rentalTotal);
        $itemCount = max(0, (int)$itemCount);

        $qualifiesByCount = ($itemCount >= self::MIN_ITEMS_FOR_DISCOUNT);
        $qualifiesByAmount = ($rentalTotal >= self::MIN_AMOUNT_FOR_DISCOUNT);

        $isApplied = ($qualifiesByCount || $qualifiesByAmount);
        $discountPercent = $isApplied ? self::DISCOUNT_PERCENT : 0;
        $discountAmount = $isApplied ? round($rentalTotal * (self::DISCOUNT_PERCENT / 100), 2) : 0.0;

        $reason = null;
        if ($qualifiesByCount && $qualifiesByAmount) {
            $reason = 'โปรโมชั่นพิเศษ: ลด 20% (เช่าครบ 3 รายการ และยอดเช่าครบ 2,000 บาท)';
        } elseif ($qualifiesByCount) {
            $reason = 'โปรโมชั่นพิเศษ: ลด 20% (เช่าครบ ' . self::MIN_ITEMS_FOR_DISCOUNT . ' รายการ)';
        } elseif ($qualifiesByAmount) {
            $reason = 'โปรโมชั่นพิเศษ: ลด 20% (ยอดเช่าครบ ' . number_format(self::MIN_AMOUNT_FOR_DISCOUNT) . ' บาท)';
        }

        $hintMessage = null;
        if (!$isApplied && $itemCount > 0) {
            $itemsNeeded = max(0, self::MIN_ITEMS_FOR_DISCOUNT - $itemCount);
            $amountNeeded = max(0.0, self::MIN_AMOUNT_FOR_DISCOUNT - $rentalTotal);
            $hintMessage = "เช่าเพิ่มอีก {$itemsNeeded} รายการ หรือเพิ่มยอดเช่าอีก ฿" . number_format($amountNeeded, 0) . " รับส่วนลดทันที 20%!";
        }

        return [
            'is_applied'        => $isApplied,
            'discount_percent'  => $discountPercent,
            'discount_amount'   => $discountAmount,
            'discount_reason'   => $reason,
            'net_rental_total'  => max(0.0, $rentalTotal - $discountAmount),
            'qualifies_by_count' => $qualifiesByCount,
            'qualifies_by_amount' => $qualifiesByAmount,
            'hint_message'      => $hintMessage,
        ];
    }
}
