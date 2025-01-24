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
            $table->string('phone');
            $table->string('landline')->nullable("N/A");
            $table->string('email');
            $table->string('website')->nullable("N/A");
            $table->string('sec')->nullable("N/A");
            $table->string('vat');
            $table->string('wht')->nullable("N/A");
            $table->string('businessNature');
            $table->string('qualifyingSystem')->nullable("N/A");
            $table->string('certifyingBody');
            $table->string('dateCertified');
            $table->string('payment');
            $table->string('status');
            $table->string('remarks')->nullable("N/A");
            $table->string('businessStyle');
            $table->string('tin');
            $table->string('createdDate')->nullable("N/A");
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
