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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->string('name');
            $table->integer('deal_type');
            $table->integer('category_id');
            $table->string('brand')->nullable();
            $table->longtext('description')->nullable();
            $table->string('slug')->unique();
            $table->decimal('original_price', 10, 2);
            $table->decimal('discounted_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('stock')->default(0)->nullable();
            $table->string('sku', 100)->unique()->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('active')->default(0);
            $table->longText('specifications')->nullable();
            $table->string('varient')->nullable();
            $table->string('delivery_days')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
