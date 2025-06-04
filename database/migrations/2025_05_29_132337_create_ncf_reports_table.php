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
        Schema::create('ncf_reports', function (Blueprint $table) {
            $table->id();
            // Step 1
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('ncfNumber')->nullable();
            $table->date('issuedDate')->nullable();
            $table->string('customerName')->nullable();
            $table->string('contactPersonName')->nullable();
            $table->string('contactPersonEmail')->nullable();
            $table->string('equipment_id')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('serial')->nullable();
            $table->string('description')->nullable();
            // Step 2
            $table->json('specificFailure')->nullable();
            $table->string('isCalibrationCompleted')->nullable();
            $table->string('isCurrentChargeableItem')->nullable();
            $table->boolean('troubleshootingStatus')->default(false);
            $table->json('correctiveAction')->nullable();
            $table->string('diagnosticFee')->nullable();
            $table->string('conditionalFee')->nullable();
            $table->string('conditionalFeeAmount')->nullable();
            // Step 3
            $table->string('ncfReportedBy')->nullable();
            $table->string('ncfReviewedBy')->nullable();
            // Step 4
            $table->string('comments')->nullable();
            $table->date('repliedDate')->nullable();
            $table->string('approvedBy')->nullable();
            $table->string('status')->nullable();
            // Timestamp and Soft Deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncf_reports');
    }
};
