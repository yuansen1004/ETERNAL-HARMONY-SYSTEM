<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('row');
            $table->integer('column');
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->string('image')->nullable();
            $table->string('occupant_name')->nullable();
            $table->integer('slot_number')->nullable(); // Slot number for identification
            $table->string('user_name')->nullable(); // User name associated with the item
            $table->json('images')->nullable(); // Store multiple images as JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
}; 