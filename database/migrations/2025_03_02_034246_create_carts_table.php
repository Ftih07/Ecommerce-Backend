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
        Schema::create('carts', function (Blueprint $table) {
            // Cart ID
            $table->id('cart_id');

            // Quantity
            $table->integer('quantity');

            // Total Price 
            $table->decimal('total_price', 10, 2);

            // Product ID & User ID
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');
            $table
                ->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->onDelete('cascade');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
