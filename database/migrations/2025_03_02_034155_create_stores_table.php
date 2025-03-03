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
        Schema::create('stores', function (Blueprint $table) {
            // Store ID
            $table->id('store_id');
 
            // Store Name
            $table->string('name');

            // Store Email
            $table->string('city');

            // Profile Image
            $table->string('profile_image')->nullable();

            // Created At and Updated At
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
