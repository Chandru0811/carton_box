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
        Schema::create('shop_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->longText('refund_policy')->nullable();
            $table->longText('cancellation_policy')->nullable();
            $table->longText('shipping_policy')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_policies');
    }
};
