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
        Schema::create('price_quote_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_quote_id')->constrained('price_quotes')->cascadeOnDelete();

            // Equipment Information / Body of the Letter
            $table->integer('item_number')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('description')->nullable();
            $table->string('serial')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('line_total')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_quote_equipment');
    }
};
