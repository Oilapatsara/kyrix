<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');

            $table->unsignedBigInteger('rental_id');

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_method', 50)->nullable();
            $table->string('status', 50)->default('pending');

            $table->string('payment_ref', 100)->nullable();
            $table->string('slip_image', 255)->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();

            $table->foreign('rental_id')
                ->references('rental_id')
                ->on('rentals')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};