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
            $table->longtext('item_description');
            $table->integer('quantity')->unsigned();
            $table->boolean('viewed_by_admin')->default(true);
            $table->boolean('viewed_by_vendor')->default(true);
            $table->decimal('unit_price', 20, 6);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
