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
        Schema::create('orders', function (Blueprint $table) {
            // Order ID
            $table->id('order_id');

            // Final Price 
            $table->decimal('final_price', 10, 2);

            // Cart ID & Payment ID
            $table
                ->foreignId('cart_id')
                ->constrained('carts', 'cart_id')
                ->onDelete('cascade');
            $table
                ->foreignId('payment_id')
                ->constrained('payments', 'payment_id')
                ->onDelete('cascade');

            // Order Date
            $table->date('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
