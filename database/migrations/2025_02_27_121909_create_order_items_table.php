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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigInteger('order_id')->unsigned()->index();
            $table->string('item_number')->unique();
            $table->bigInteger('product_id')->unsigned()->index();
            $table->bigInteger('seller_id')->unsigned()->index();
            $table->longText('item_description');
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 20, 6);
            $table->date('delivery_date')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('discount', 20, 6)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('deal_type')->nullable();
            $table->date('service_date')->nullable();
            $table->time('service_time')->nullable();
            $table->decimal('shipping', 20, 6)->default(0);
            $table->decimal('packaging', 20, 6)->default(0);
            $table->decimal('handling', 20, 6)->default(0);
            $table->decimal('taxes', 20, 6)->default(0);
            $table->decimal('shipping_weight', 20, 6)->default(0);
            $table->boolean('viewed_by_admin')->default(true);
            $table->boolean('viewed_by_vendor')->default(true);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
