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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable(); //imported
            //Basic Information
            $table->string('name')->nullable(); //imported
            $table->string('nickname')->nullable();
            $table->string('address')->nullable(); //imported
            $table->string('dateCertified')->nullable(); //imported
            $table->string('tradeName')->nullable();
            $table->string('qualifyingSystem')->nullable(); //imported
            $table->string('certifyingBody')->nullable(); //imported
            $table->string('remarks', 1000)->nullable(); //imported
            $table->string('referredBy')->nullable();
            //BIR Information
            $table->string('tin')->nullable(); //imported
            $table->string('sec')->nullable(); //imported
            $table->string('withHoldingTax')->nullable(); //imported
            $table->string('businessNature')->nullable(); //imported
            $table->string('businessStyle')->nullable(); //imported
            $table->string('industry')->nullable();
            $table->string('vat')->nullable(); //imported
            $table->boolean('vatExempt')->default(false);
            $table->string('vatExemptCertificateNo')->nullable();
            $table->string('vatExemptValidity')->nullable();
            $table->boolean('othersForVat')->default(false);
            $table->string('otherVat')->nullable();
            //Contact Details
            $table->string('mobile1')->nullable();
            $table->string('areaCodeTelephone1')->nullable();
            $table->string('telephone1')->nullable(); //imported
            $table->string('mobile2')->nullable();
            $table->string('areaCodeTelephone2')->nullable();
            $table->string('telephone2')->nullable(); //imported
            $table->string('email')->nullable(); //imported
            $table->string('website')->nullable(); //imported
            $table->string('status')->nullable(); //imported
            $table->string('payment')->nullable(); //imported
            $table->boolean('othersForPayment')->default(false);
            $table->string('otherPayment')->nullable();
            //Other Imported Data
            $table->string('createdDate')->nullable(); //imported
            //Identifier if the customer is imported
            $table->boolean('isCustomerImported')->default(false); 
            //Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
