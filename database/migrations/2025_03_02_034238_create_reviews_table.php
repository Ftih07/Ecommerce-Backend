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
        Schema::create('reviews', function (Blueprint $table) {
            // Review ID
            $table->id('review_id');

            // User ID
            $table
                ->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->onDelete('cascade');
 
            // Product ID
            $table
                ->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            // Rating
            $table->integer('rating');

            // Review
            $table->text('review');

            // Created at Updated at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
