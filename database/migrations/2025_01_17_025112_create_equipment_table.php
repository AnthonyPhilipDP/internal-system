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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id()->from(500);
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('make');
            $table->string('model');
            $table->string('serial');
            $table->string('description');
            $table->string('lab');
            $table->string('calType');
            $table->string('category');
            $table->string('acce');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
