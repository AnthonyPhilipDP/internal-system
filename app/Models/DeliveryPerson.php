<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'ar_id',
        'name',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
