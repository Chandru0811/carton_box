<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('saved_items', function (Blueprint $table) {
            $table->string('cart_number')->nullable()->after('user_id');
        });
    }
    
    public function down()
    {
        Schema::table('saved_items', function (Blueprint $table) {
            $table->dropColumn('cart_number');
        });
    }
};
