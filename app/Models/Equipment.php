<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Accessory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'id',
        'customer_id',
        'manufacturer',
        'model',
        'serial',
        'description',
        'inspection',
        'lab',
        'calType',
        'category',
        'inDate',
        //For Status
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
        'worksheet',
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
        //For Documents Update
        'calibrationDocument',
        'drNoDocument',
        'documentReleasedDate',
        'documentReceivedBy',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function accessory() {
        return $this->hasMany(Accessory::class);
    } 
}
