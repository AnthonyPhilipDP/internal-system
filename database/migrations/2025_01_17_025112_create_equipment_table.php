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
            $table->unsignedBigInteger('transaction_id')->nullable(); //imported
            //Details
            $table->unsignedBigInteger('customer_id')->nullable(); //imported
            $table->string('equipment_id')->nullable(); //imported
            $table->string('make')->nullable(); //imported
            $table->string('model')->nullable(); //imported
            $table->string('serial')->nullable(); //imported
            $table->string('description')->nullable(); //imported
            $table->string('laboratory')->nullable(); //imported
            $table->string('calibrationType')->nullable(); //imported
            $table->string('category')->nullable(); //imported
            $table->json('inspection')->nullable();
            $table->string('inDate')->nullable();  //imported
            $table->string('decisionRule')->nullable();
            $table->string('status')->nullable(); //imported
            $table->string('ar_id')->nullable(); //imported
            $table->string('gatePass')->nullable();
            //Status
            $table->unsignedBigInteger('worksheet_id')->nullable();
            $table->string('calibrationProcedure')->nullable(); //imported
            $table->string('previousCondition')->nullable(); //imported
            $table->string('inCondition')->nullable(); //imported
            $table->string('outCondition')->nullable(); //imported
            $table->string('service')->nullable(); //imported
            $table->boolean('intermediateCheck')->default(false); //imported
            $table->string('code_range')->nullable(); //imported
            $table->string('reference')->nullable(); //imported
            $table->string('standardsUsed')->nullable(); //imported
            $table->string('temperature')->nullable(); //imported
            $table->string('humidity')->nullable(); //imported
            $table->string('validation')->nullable(); //imported
            $table->string('validatedBy')->nullable();
            $table->boolean('ncfReport')->default(false);
            //Timeline
            $table->string('calibrationDate')->nullable(); //imported
            $table->string('calibrationInterval')->nullable(); //imported
            $table->string('calibrationDue')->nullable(); //imported
            $table->string('outDate')->nullable(); //imported
            $table->string('poNoCalibration')->nullable(); //imported
            $table->string('poNoRealign')->nullable(); //imported
            $table->string('poNoRepair')->nullable(); //imported
            $table->string('prNo')->nullable(); //imported
            //For Documents Update
            $table->string('drNoDocument')->nullable(); //DocDR //imported
            $table->string('documentReleasedDate')->nullable(); //DocDateReleased //imported
            $table->string('documentReceivedBy')->nullable(); //PersonReceivedDoc
            $table->string('comments', 1500)->nullable(); //imported
            //Other Columns that needs to be imported
            //but still UNUSED in the system (for future use)
            $table->string('worksheet')->nullable();
            $table->string('oldInspection')->nullable();
            $table->string('oldAccessories', 1000)->nullable();
            $table->string('DR_No2')->nullable();
            $table->string('Item_No')->nullable();
            $table->string('Invoiced')->nullable();
            $table->string('certifiyWithSpaces')->nullable();
            $table->string('DR_No3')->nullable();
            $table->string('DR_No4')->nullable();
            $table->string('Num_Pages')->nullable();
            $table->string('Remarks')->nullable();
            $table->string('PriorityRemarks')->nullable();
            $table->string('DrNoDocReleased')->nullable();
            $table->string('assignedTo')->nullable();
            //Imported Booleans
            $table->boolean('DR')->default(false);                                                    
            $table->boolean('RP')->default(false);                                                    
            $table->boolean('rep')->default(false);                                                    
            $table->boolean('certify')->default(false);                                                    
            $table->boolean('DAILY_rep')->default(false);      
            //Identifier if the equipment is imported
            $table->boolean('isEquipmentImported')->default(false);  
            //Client Exclusive
            $table->boolean('isClientExclusive')->default(false);  
            $table->string('exclusive_id')->nullable();                                        
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
