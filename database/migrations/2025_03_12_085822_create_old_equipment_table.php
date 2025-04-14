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
        Schema::create('old_equipment', function (Blueprint $table) {
            /*
             * The column names are INTENTIONALLY left as they are
             * to maintain compatibility with old data and references.
             * I have to follow the naming convention of the old database.
             * because this is preferred by the company.
             * The data is intended for viewing only anyways.
             */
            $table->id();
            $table->string('trans no')->nullable();
            $table->string('cert no')->nullable();
            $table->string('date in')->nullable();
            $table->string('customer id')->nullable();
            $table->string('po')->nullable();
            $table->string('realignpo')->nullable();
            $table->string('repairpo')->nullable();
            $table->string('pr')->nullable();
            $table->string('equip id')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('description')->nullable();
            $table->string('serial no')->nullable();
            $table->string('cal date')->nullable();
            $table->string('cal due')->nullable();
            $table->string('cod/range')->nullable();
            $table->string('cal procedure')->nullable();
            $table->string('prev condition')->nullable();
            $table->string('condition in')->nullable();
            $table->string('condition out')->nullable();
            $table->string('validation')->nullable();
            $table->string('temp')->nullable();
            $table->string('humidity')->nullable();
            $table->string('cal interval')->nullable();
            $table->string('form no')->nullable();
            $table->string('ref')->nullable();
            $table->string('category')->nullable();
            $table->string('service')->nullable();
            $table->string('status')->nullable();
            $table->text('comments')->nullable();
            $table->string('date out')->nullable();
            $table->string('visual insp')->nullable();
            $table->text('accessories')->nullable();
            $table->string('ack receipt no')->nullable();
            $table->string('dr_no2')->nullable();
            $table->string('standards used')->nullable();
            $table->string('invoiced')->nullable();
            $table->string('dr_no3')->nullable();
            $table->string('docdr')->nullable();
            $table->string('dr_no4')->nullable();
            $table->string('num_pages')->nullable();
            $table->string('remarks')->nullable();
            $table->string('priority remarks')->nullable();
            $table->string('intermediate check')->nullable();
            $table->string('caltype')->nullable();
            $table->string('laboratory')->nullable();
            $table->string('DocDateReleased')->nullable();
            $table->string('DrNoDocReleased')->nullable();
            $table->string('assignedTo')->nullable();
            $table->string('PersonReceivedDoc')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_equipment');
    }
};
