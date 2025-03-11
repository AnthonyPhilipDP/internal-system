<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentOld extends Model
{
    protected $fillable = [
        'id',
        'transaction_id',
        'equipment_id',
        'ar_id',
        'customer_id',
        'worksheet_id',
        'manufacturer',
        'model',
        'serial',
        'description',
        'inspection',
        'lab',
        'calType',
        'category',
        'inDate',
        'calibrationProcedure',
        'previousCondition',
        'inCondition',
        'outCondition',
        'service',
        'intermediateCheck',
        'status',
        'comments',
        'code_range',
        'reference',
        'standardsUsed',
        'validation',
        'validatedBy',
        'temperature',
        'humidity',
        'ncfReport',
        'calibrationDate',
        'calibrationInterval',
        'calibrationDue',
        'outDate',
        'poNoCalibration',
        'poNoRealign',
        'poNoRepair',
        'prNo',
        'calibrationDocument',
        'drNoDocument',
        'documentReleasedDate',
        'documentReceivedBy',
    ];
}
