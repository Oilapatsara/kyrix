<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'condition_status')) {
                $table->string('condition_status', 20)->nullable()->after('status'); // 'good', 'damaged'
                $table->string('deposit_status', 20)->default('pending')->after('condition_status'); // 'pending', 'refunded', 'forfeited'
                $table->decimal('deposit_refund_amount', 10, 2)->default(0)->after('deposit_status');
                $table->text('damage_note')->nullable()->after('deposit_refund_amount');
                $table->string('damage_image', 255)->nullable()->after('damage_note');
                $table->string('refund_slip', 255)->nullable()->after('damage_image');
                $table->timestamp('inspected_at')->nullable()->after('refund_slip');
            }
        });

        // Set products default deposit to 100.00
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('deposit', 10, 2)->default(100.00)->change();
        });

        // Update existing products deposit to 100
        DB::table('products')->update(['deposit' => 100.00]);
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'condition_status',
                'deposit_status',
                'deposit_refund_amount',
                'damage_note',
                'damage_image',
                'refund_slip',
                'inspected_at'
            ]);
        });
    }
};
