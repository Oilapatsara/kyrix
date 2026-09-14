<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure first_name, last_name, email in customers are nullable so registration never throws 1364 error
        if (Schema::hasColumn('customers', 'first_name')) {
            DB::statement("ALTER TABLE customers MODIFY first_name VARCHAR(100) NULL");
        } else {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('first_name', 100)->nullable()->after('user_id');
            });
        }

        if (Schema::hasColumn('customers', 'last_name')) {
            DB::statement("ALTER TABLE customers MODIFY last_name VARCHAR(100) NULL");
        } else {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('last_name', 100)->nullable()->after('first_name');
            });
        }

        if (Schema::hasColumn('customers', 'email')) {
            DB::statement("ALTER TABLE customers MODIFY email VARCHAR(150) NULL");
        } else {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('email', 150)->nullable()->after('last_name');
            });
        }
    }

    public function down(): void
    {
        // no-op
    }
};
