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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('owner_id')->unsigned();
            $table->text('name')->nullable();
            $table->text('legal_name')->nullable();
            $table->string('company_registeration_no');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->longtext('description')->nullable();
            $table->string('external_url')->nullable();
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('zip_code');
            $table->string('country');
            $table->string('state');
            $table->string('current_billing_plan')->default('free');
            $table->string('account_holder')->nullable();
            $table->string('account_type')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('payment_id')->nullable();
            $table->boolean('active')->nullable()->default(0);
            $table->decimal('shop_ratings', 3, 2)->nullable();
            $table->string('shop_type')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('map_url')->nullable();
            $table->decimal('shop_lattitude')->nullable();
            $table->decimal('shop_longtitude')->nullable();
            $table->text('address')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
