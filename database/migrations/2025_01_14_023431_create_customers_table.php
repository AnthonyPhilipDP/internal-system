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
            $table->string('fax')->nullable();
            $table->string('email');
            $table->string('site')->nullable();
            $table->string('SEC')->nullable();
            $table->string('VAT');
            $table->string('WTP')->nullable();
            $table->string('main_act');
            $table->string('QS')->nullable();
            $table->string('certifying_body');
            $table->string('date_certified');
            $table->string('payment');
            $table->string('status');
            $table->string('remarks')->nullable();
            $table->string('business_system');
            $table->string('tin')->nullable();
            $table->string('acct_created')->nullable();
            $table->timestamps();
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
