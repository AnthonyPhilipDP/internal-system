<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customer_id',
        'make',
        'model',
        'serial',
        'description',
        'lab',
        'calType',
        'category',
        'acce',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
