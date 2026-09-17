<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // เชื่อมรูปภาพกับชื่อสี เช่น 'ดำ', 'เทา', 'ทอง'
            // null = รูปหลัก (ไม่ผูกกับสีใดโดยเฉพาะ)
            $table->string('color_name', 100)->nullable()->after('is_main');
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn('color_name');
        });
    }
};
