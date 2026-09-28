<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            // Imported databases may lose AUTO_INCREMENT while retaining the primary key.
            DB::statement('ALTER TABLE users MODIFY user_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Preserve the original schema's automatic IDs and all existing accounts.
    }
};
