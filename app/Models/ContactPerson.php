<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactPerson extends Model
{

    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'isContactImported' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }
}
