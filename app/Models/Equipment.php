<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Accessory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

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
        'calibrationProcedure',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function accessory() {
        return $this->hasMany(Accessory::class);
    } 
}
