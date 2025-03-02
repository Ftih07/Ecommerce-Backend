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
        Schema::create('product_images', function (Blueprint $table) {
            // Product Images ID
            $table->id('product_images_id');

            // Product Images Name
            $table->string('name');

            // Product Images Path
            $table->string('path');

            // Product ID
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            // Created At & Updated At
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
