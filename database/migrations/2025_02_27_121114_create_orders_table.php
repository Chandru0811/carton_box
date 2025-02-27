<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->bigInteger('shop_id')->unsigned()->nullable();
            $table->enum('order_type', ['product', 'service'])->index();
            $table->text('notes')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_status')->nullable();
            $table->date('service_date')->nullable();
            $table->time('service_time')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('item_count')->default(0);
            $table->decimal('total', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->nullable();
            $table->decimal('shipping', 8, 2)->nullable();
            $table->decimal('packaging', 8, 2)->nullable();
            $table->decimal('handling', 8, 2)->nullable();
            $table->decimal('taxes', 8, 2)->nullable();
            $table->decimal('grand_total', 8, 2)->nullable();
            $table->decimal('shipping_weight', 8, 2)->nullable();
            $table->string('billing_address')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('shipping_cost', 8, 2)->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('tracking_id')->nullable();
            $table->boolean('coupon_applied')->nullable();
            $table->boolean('send_invoice_to_customer')->nullable();
            $table->boolean('approved')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
