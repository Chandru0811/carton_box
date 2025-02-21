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
        Schema::create(
            'countries',
            function (Blueprint $table) {
                $table->id();
                $table->string('country_name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('flag')->nullable();
                $table->string('currency_symbol')->nullable();
                $table->string('currency_code')->nullable();
                $table->string('social_links')->nullable();
                $table->string('color_code')->nullable();
                $table->string('country_code')->nullable();
                $table->text('address')->nullable();
                $table->softDeletes();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
