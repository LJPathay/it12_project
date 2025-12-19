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
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->integer('quantity');
            $table->integer('remaining_quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('received_date');
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'depleted', 'expired'])->default('active');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['inventory_id', 'status']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
