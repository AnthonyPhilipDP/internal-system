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
            $table->unsignedBigInteger('invoice_number')->unique();
            $table->date('invoice_date')->nullable();
            $table->string('poNoCalibration')->nullable();
            $table->string('yourRef')->nullable();
            $table->string('pmsiRefNo')->nullable();
            $table->string('freeOnBoard')->nullable();
            $table->string('businessSystem')->nullable();
            $table->string('tin')->nullable();
            $table->string('service')->nullable();
            $table->string('payment')->nullable();

            // Bottom
            $table->string('comments')->nullable();
            $table->string('subTotal')->nullable();
            
            $table->boolean('applyToAll')->default(false);
            $table->string('global_less_type')->nullable();
            $table->string('global_less_percentage')->nullable();
            $table->string('global_less_amount')->nullable();
            $table->string('global_charge_type')->nullable();
            $table->string('global_charge_percentage')->nullable();
            $table->string('global_charge_amount')->nullable();

            $table->boolean('applyEwt')->default(false);
            $table->string('ewt_percentage')->nullable();
            $table->string('ewt_amount')->nullable();
            $table->boolean('showEwt')->default(false);
            
            $table->boolean('vatToggle')->default(false); // Vat Inclusive
            $table->string('vatAmount')->nullable();
            
            $table->string('currency')->nullable();
            $table->string('total')->nullable();
            $table->string('amountInWords')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
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
