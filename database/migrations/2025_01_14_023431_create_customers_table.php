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
            $table->string('name');
            $table->string('address');
            $table->string('mobile1')->nullable();
            $table->string('mobile2')->nullable();
            $table->string('telephone1')->nullable();
            $table->string('telephone2')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('sec')->nullable();
            $table->string('vat')->nullable();
            $table->string('withHoldingTax')->nullable();
            $table->string('businessNature')->nullable();
            $table->string('qualifyingSystem')->nullable();
            $table->string('certifyingBody')->nullable();
            $table->string('dateCertified')->nullable();
            $table->string('payment');
            $table->string('status');
            $table->string('remarks', 1000)->nullable();
            $table->string('businessStyle')->nullable();
            $table->string('tin');
            $table->string('createdDate')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('nickname')->nullable();
            $table->string('tradeName')->nullable();
            $table->string('industry')->nullable();
            $table->string('referredBy')->nullable();
            $table->string('otherVat')->nullable();
            $table->string('otherPayment')->nullable();
            $table->string('vatExemptCertificateNo')->nullable();
            $table->string('vatExemptValidity')->nullable();
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
