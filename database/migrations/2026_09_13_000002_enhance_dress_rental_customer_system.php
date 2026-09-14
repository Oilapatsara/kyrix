<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
                $table->boolean('is_popular')->default(false)->after('is_featured');
                $table->boolean('is_new')->default(true)->after('is_popular');
                $table->string('available_sizes', 100)->nullable()->after('size');
                $table->string('available_colors', 150)->nullable()->after('color');
                $table->string('bust', 50)->nullable()->after('available_colors');
                $table->string('waist', 50)->nullable()->after('bust');
                $table->string('hips', 50)->nullable()->after('waist');
                $table->string('length', 50)->nullable()->after('hips');
                $table->integer('views_count')->default(0)->after('stock');
                $table->integer('rental_count')->default(0)->after('views_count');
            }
        });

        // 2. Enhance rentals table
        Schema::table('rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('rentals', 'rental_code')) {
                $table->string('rental_code', 50)->nullable()->unique()->after('rental_id');
                $table->string('service_type', 100)->nullable()->after('deposit_amount');
                $table->decimal('service_fee', 10, 2)->default(0)->after('service_type');
                $table->string('delivery_method', 50)->default('pickup')->after('service_fee');
                $table->text('delivery_address')->nullable()->after('delivery_method');
                $table->string('recipient_phone', 30)->nullable()->after('delivery_address');
                $table->string('tracking_number', 100)->nullable()->after('recipient_phone');
                $table->string('return_tracking_no', 100)->nullable()->after('tracking_number');
                $table->string('status', 50)->default('pending_payment')->change();
            }
        });

        // 3. Enhance rental_details table
        Schema::table('rental_details', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_details', 'selected_size')) {
                $table->string('selected_size', 50)->nullable()->after('quantity');
                $table->string('selected_color', 50)->nullable()->after('selected_size');
                $table->integer('rental_days')->default(1)->after('selected_color');
            }
        });

        // 4. Create reviews table
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id('review_id');
                $table->unsignedBigInteger('rental_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('customer_id');
                $table->tinyInteger('rating')->default(5);
                $table->text('comment')->nullable();
                $table->string('image_path', 255)->nullable();
                $table->enum('status', ['published', 'hidden'])->default('published');
                $table->timestamps();

                $table->foreign('rental_id')->references('rental_id')->on('rentals')->nullOnDelete()->cascadeOnUpdate();
                $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete()->cascadeOnUpdate();
                $table->foreign('customer_id')->references('customer_id')->on('customers')->cascadeOnDelete()->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');

        Schema::table('rental_details', function (Blueprint $table) {
            $table->dropColumn(['selected_size', 'selected_color', 'rental_days']);
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'rental_code',
                'service_type',
                'service_fee',
                'delivery_method',
                'delivery_address',
                'recipient_phone',
                'tracking_number',
                'return_tracking_no'
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'is_featured',
                'is_popular',
                'is_new',
                'available_sizes',
                'available_colors',
                'bust',
                'waist',
                'hips',
                'length',
                'views_count',
                'rental_count'
            ]);
        });
    }
};
