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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('pack')->nullable()->after('delivery_days');
            $table->decimal('box_length', 8, 2)->nullable()->after('pack');
            $table->decimal('box_width', 8, 2)->nullable()->after('box_length');
            $table->decimal('box_height', 8, 2)->nullable()->after('box_width');
            $table->integer('stock_quantity')->default(0)->nullable()->after('box_height');
            $table->string('unit')->nullable()->after('stock_quantity');
            $table->unsignedBigInteger('country_id')->nullable()->after('unit');

            // Foreign Key Constraint
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['pack', 'box_length', 'box_width', 'box_height', 'stock_quantity', 'country_id']);
        });
    }
};
