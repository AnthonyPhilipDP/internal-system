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
        Schema::create('potential_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            //Basic Information
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('address')->nullable();
            $table->string('dateCertified')->nullable();
            $table->string('tradeName')->nullable();
            $table->string('qualifyingSystem')->nullable();
            $table->string('certifyingBody')->nullable();
            $table->string('remarks', 1000)->nullable();
            $table->string('referredBy')->nullable();
            //BIR Information
            $table->string('tin')->nullable();
            $table->string('sec')->nullable();
            $table->string('withHoldingTax')->nullable();
            $table->string('businessNature')->nullable();
            $table->string('businessStyle')->nullable();
            $table->string('industry')->nullable();
            $table->string('vat')->nullable();
            $table->boolean('vatExempt')->default(false);
            $table->string('vatExemptCertificateNo')->nullable();
            $table->string('vatExemptValidity')->nullable();
            $table->boolean('othersForVat')->default(false);
            $table->string('otherVat')->nullable();
            //Contact Details
            $table->string('mobile1')->nullable();
            $table->string('areaCodeTelephone1')->nullable();
            $table->string('telephone1')->nullable();
            $table->string('mobile2')->nullable();
            $table->string('areaCodeTelephone2')->nullable();
            $table->string('telephone2')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->nullable();
            $table->string('payment')->nullable();
            $table->boolean('othersForPayment')->default(false);
            $table->string('otherPayment')->nullable();
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
        Schema::dropIfExists('potential_customers');
    }
};
