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
            // Produck ID
            $table->id('product_id');

            // Product Name 
            $table->string('name');

            // Thumbnail Image
            $table->string('thumbnail_image')->nullable();

            // Stock
            $table->integer('stock')->default(0);

            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Description
            $table->text('description')->nullable();

            // Price
            $table->decimal('price', 10, 2);

            // Store ID
            $table
                ->foreignId('store_id')
                ->constrained('stores', 'store_id')
                ->onDelete('cascade');

            // Category ID
            $table
                ->foreignId('category_id')
                ->constrained('categories', 'category_id')
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
        Schema::dropIfExists('products');
    }
};
