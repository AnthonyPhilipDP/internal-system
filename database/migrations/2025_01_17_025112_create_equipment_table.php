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
            $table->id();
            $table->unsignedBigInteger('transaction_id')->nullable();
            //Details
            $table->foreignId('customer_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->string('equipment_id')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('serial')->nullable();
            $table->string('description')->nullable();
            $table->string('laboratory')->nullable();
            $table->string('calibrationType')->nullable();
            $table->string('category')->nullable();
            $table->json('inspection')->nullable();
            $table->string('inDate')->nullable();
            $table->string('decisionRule')->nullable();
            $table->string('status')->nullable();
            $table->string('ar_id')->nullable();
            $table->string('gatePass')->nullable();
            //Status
            $table->unsignedBigInteger('worksheet_id')->nullable();
            $table->string('calibrationProcedure')->nullable();
            $table->string('previousCondition')->nullable();
            $table->string('inCondition')->nullable();
            $table->string('outCondition')->nullable();
            $table->string('service')->nullable();
            $table->boolean('intermediateCheck')->default(false);
            $table->string('code_range')->nullable();
            $table->string('reference')->nullable();
            $table->string('standardsUsed')->nullable();
            $table->string('temperature')->nullable();
            $table->string('humidity')->nullable();
            $table->string('validation')->nullable();
            $table->string('validatedBy')->nullable();
            $table->string('ncfReport')->nullable();
            //Timeline
            $table->string('calibrationDate')->nullable();
            $table->string('calibrationInterval')->nullable();
            $table->string('calibrationDue')->nullable();
            $table->string('outDate')->nullable();
            $table->string('poNoCalibration')->nullable();
            $table->string('poNoRealign')->nullable();
            $table->string('poNoRepair')->nullable();
            $table->string('prNo')->nullable();
            //For Documents Update
            $table->string('calibrationDocument')->nullable();
            $table->string('drNoDocument')->nullable();
            $table->string('documentReleasedDate')->nullable();
            $table->string('documentReceivedBy')->nullable();
            $table->string('comments')->nullable();
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
        Schema::dropIfExists('equipment');
    }
};
