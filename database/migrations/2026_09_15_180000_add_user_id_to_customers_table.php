<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('customer_id');
                $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete()->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
