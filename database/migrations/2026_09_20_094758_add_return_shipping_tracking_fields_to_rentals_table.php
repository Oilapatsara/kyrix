<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มข้อมูลการติดตามพัสดุส่งคืนจากลูกค้า
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('return_shipping_carrier', 100)
                ->nullable()
                ->after('return_tracking_no');

            $table->string('return_shipping_status', 100)
                ->nullable()
                ->after('return_shipping_carrier');

            $table->dateTime('return_shipped_at')
                ->nullable()
                ->after('return_shipping_status');

            $table->dateTime('return_estimated_delivery_at')
                ->nullable()
                ->after('return_shipped_at');
        });
    }

    /**
     * ยกเลิก Migration
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'return_shipping_carrier',
                'return_shipping_status',
                'return_shipped_at',
                'return_estimated_delivery_at',
            ]);
        });
    }
};