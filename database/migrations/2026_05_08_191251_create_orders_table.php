<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('pickup_address');

            $table->text('delivery_address');

            $table->enum('status', [
                'pending',
                'assigned',
                'delivered',
                'cancelled'
            ])->default('pending');

            $table->enum('priority', [
                'normal',
                'vip'
            ])->default('normal');

            $table->decimal('weight_kg', 10, 2);

            $table->enum('delivery_option', [
                'leave_at_door',
                'neighbor',
                'call_customer'
            ])->default('call_customer');

            $table->timestamp('estimated_delivery_time')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};