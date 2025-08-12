<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_inventory_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->decimal('custom_price', 10, 2)->nullable(); // Store custom price for this specific order
            $table->decimal('original_price', 10, 2)->nullable(); // Store original price for reference
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
            
            // Ensure unique combination
            $table->unique(['order_id', 'inventory_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_inventory_item');
    }
};
