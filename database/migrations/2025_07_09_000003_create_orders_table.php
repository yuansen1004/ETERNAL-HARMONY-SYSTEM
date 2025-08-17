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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->onDelete('cascade');
            $table->timestamp('order_date')->useCurrent();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['full_paid', 'installment']);
            $table->integer('installment_duration')->nullable();
            $table->decimal('monthly_payment', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->json('receipt_details')->nullable();
            $table->enum('package_status', ['pending', 'complete'])->default('pending');
            $table->boolean('installment_paid')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('customer_id');
            $table->index('package_id');
            $table->index('inventory_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};