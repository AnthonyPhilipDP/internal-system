<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactPerson extends Model
{

    use HasFactory;

    protected $fillable = [
        'id',
        'equipment_id',
        'name',
        'department',
        'position',
        'contact1',
        'contact2',
        'email',
        'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }
}
