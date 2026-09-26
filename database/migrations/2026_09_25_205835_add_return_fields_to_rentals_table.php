<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มข้อมูลสำหรับระบบคืนชุด
     *
     * สถานะ:
     * not_returned = ยังไม่คืน
     * returned_requested = แจ้งคืนแล้ว
     * returned = คืนชุดแล้ว
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {

            // วิธีคืนชุด: parcel / store
            if (!Schema::hasColumn('rentals', 'return_method')) {
                $table->string('return_method', 30)
                    ->nullable()
                    ->comment('วิธีคืนชุด: parcel หรือ store');
            }

            // เลขพัสดุ กรณีลูกค้าเลือกส่งพัสดุ
            if (!Schema::hasColumn('rentals', 'return_tracking_number')) {
                $table->string('return_tracking_number', 100)
                    ->nullable()
                    ->comment('เลขพัสดุสำหรับการคืนชุด');
            }

            // สถานะการคืนชุด
            if (!Schema::hasColumn('rentals', 'return_status')) {
                $table->string('return_status', 30)
                    ->default('not_returned')
                    ->comment('สถานะ: not_returned, returned_requested, returned');
            }

            // วันที่ลูกค้าแจ้งคืนชุด
            if (!Schema::hasColumn('rentals', 'return_requested_at')) {
                $table->timestamp('return_requested_at')
                    ->nullable()
                    ->comment('วันที่ลูกค้าแจ้งคืนชุด');
            }

            // วันที่เจ้าของร้านยืนยันรับคืน
            if (!Schema::hasColumn('rentals', 'return_received_at')) {
                $table->timestamp('return_received_at')
                    ->nullable()
                    ->comment('วันที่เจ้าของร้านรับคืนชุด');
            }
        });
    }

    /**
     * ย้อนกลับ migration
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {

            $columns = [];

            if (Schema::hasColumn('rentals', 'return_method')) {
                $columns[] = 'return_method';
            }

            if (Schema::hasColumn('rentals', 'return_tracking_number')) {
                $columns[] = 'return_tracking_number';
            }

            if (Schema::hasColumn('rentals', 'return_status')) {
                $columns[] = 'return_status';
            }

            if (Schema::hasColumn('rentals', 'return_requested_at')) {
                $columns[] = 'return_requested_at';
            }

            if (Schema::hasColumn('rentals', 'return_received_at')) {
                $columns[] = 'return_received_at';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};