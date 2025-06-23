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
            $table->string('unit_price')->nullable();
            $table->string('equipment_subtotal')->nullable();
            $table->string('less_type')->nullable();
            $table->string('less_percentage')->nullable();
            $table->string('less_amount')->nullable();
            $table->string('charge_type')->nullable();
            $table->string('charge_percentage')->nullable();
            $table->string('charge_amount')->nullable();
            $table->string('line_total')->nullable();
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
