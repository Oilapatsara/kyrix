<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('shipping_carrier', 100)
                ->nullable()
                ->after('tracking_number');

            $table->string('shipping_status', 100)
                ->nullable()
                ->after('shipping_carrier');

            $table->text('tracking_url')
                ->nullable()
                ->after('shipping_status');

            $table->dateTime('shipped_at')
                ->nullable()
                ->after('tracking_url');

            $table->dateTime('estimated_delivery_at')
                ->nullable()
                ->after('shipped_at');

            $table->dateTime('return_due_at')
                ->nullable()
                ->after('estimated_delivery_at');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_carrier',
                'shipping_status',
                'tracking_url',
                'shipped_at',
                'estimated_delivery_at',
                'return_due_at',
            ]);
        });
    }
};