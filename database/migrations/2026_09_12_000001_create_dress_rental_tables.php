<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->enum('role', ['owner', 'customer'])->default('customer');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 100);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->unsignedBigInteger('category_id');
            $table->string('product_code', 50)->unique();
            $table->string('product_name', 150);
            $table->text('description')->nullable();
            $table->string('size', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->decimal('rental_price', 10, 2)->default(0);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->integer('stock')->default(1);
            $table->enum('status', ['available', 'rented', 'maintenance', 'inactive'])->default('available');
            $table->timestamps();
            $table->foreign('category_id')->references('category_id')->on('categories')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('product_id');
            $table->string('image_path', 255);
            $table->boolean('is_main')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('product_id')->references('product_id')->on('products')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->unsignedBigInteger('customer_id');
            $table->date('rental_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'renting', 'returned', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreign('customer_id')->references('customer_id')->on('customers')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('rental_details', function (Blueprint $table) {
            $table->id('rental_detail_id');
            $table->unsignedBigInteger('rental_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('rental_id')->references('rental_id')->on('rentals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('product_id')->references('product_id')->on('products')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('rental_id');
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->dateTime('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'transfer', 'qr', 'other'])->default('transfer');
            $table->string('slip_image', 255)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreign('rental_id')->references('rental_id')->on('rentals')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('rental_details');
        Schema::dropIfExists('rentals');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('users');
    }
};
