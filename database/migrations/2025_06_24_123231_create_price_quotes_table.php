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
        Schema::create('price_quotes', function (Blueprint $table) {
            $table->id();
            // Customer Information / Heading
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('carbon_copy')->nullable();
            $table->string('subject')->nullable();
            $table->string('price_quote_date')->nullable();
            $table->string('price_quote_number')->nullable();
            $table->string('customer_ref')->nullable();
            $table->string('customer_fax')->nullable();
            $table->string('pmsi_fax')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->string('quote_period')->nullable();

            // Letter Initial Information
            $table->string('salutation')->nullable();
            $table->string('introduction')->nullable();
            $table->text('note')->nullable();

            $table->string('subtotal')->nullable();
            $table->boolean('vat')->default(false);
            $table->string('vat_amount')->nullable();
            $table->string('total')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_quotes');
    }
};
