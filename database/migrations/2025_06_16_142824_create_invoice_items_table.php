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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->string('transaction_id')->nullable();
            $table->integer('item_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('less_type', 5, 2)->nullable();
            $table->decimal('less_percentage', 5, 2)->nullable();
            $table->decimal('less_amount', 12, 2)->nullable();
            $table->decimal('charge_type', 5, 2)->nullable();
            $table->decimal('charge_percentage', 5, 2)->nullable();
            $table->decimal('charge_amount', 12, 2)->nullable();
            $table->decimal('line_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
