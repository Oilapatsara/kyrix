<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
                $table->string('discount_reason', 150)->nullable()->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'discount_amount')) {
                $table->dropColumn(['discount_amount', 'discount_reason']);
            }
        });
    }
};
