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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Top
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('contactPerson')->nullable();
            $table->string('carbonCopy')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('poNoCalibration')->nullable();
            $table->string('yourRef')->nullable();
            $table->string('pmsiRefNo')->nullable();
            $table->string('freeOnBoard')->nullable();
            $table->string('businessSystem')->nullable();
            $table->string('tin')->nullable();
            $table->string('service')->nullable();
            $table->string('payment')->nullable();
            // Middle
            $table->string('item_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('unitPrice', 10, 2)->nullable();
            $table->decimal('equipmentTotal', 10, 2)->nullable();
            $table->string('amountInWords')->nullable();
            // Bottom
            $table->string('comments')->nullable();
            $table->string('subTotal')->nullable();

            $table->string('lessType')->nullable();
            $table->string('lessPercentage')->nullable();
            $table->string('lessAmount')->nullable();

            $table->string('chargeType')->nullable();
            $table->string('chargePercentage')->nullable();
            $table->string('chargeAmount')->nullable();

            $table->boolean('vatToggle')->default(false);

            $table->string('total')->nullable();
            
            $table->timestamps();
            // vat inclusive
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
