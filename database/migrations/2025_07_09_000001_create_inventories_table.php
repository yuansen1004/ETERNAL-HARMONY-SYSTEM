<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name'); // Slot name, e.g., Lot A
            $table->enum('category', ['columbarium', 'ancestor_pedestal', 'ancestral_tablet', 'burial_plot']);
            $table->integer('rows');
            $table->integer('columns');
            $table->json('row_prices')->nullable(); // Store per-row prices as JSON
            $table->decimal('price', 10, 2)->nullable(); // Base price for the inventory
            $table->json('images')->nullable(); // Store multiple images as JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
}; 