<?php

namespace App\Models;

use App\Models\PotentialCustomer;
use Illuminate\Database\Eloquent\Model;

class PotentialCustomerContactPerson extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function potentialCustomer()
    {
        return $this->belongsTo(PotentialCustomer::class);
    }

    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }
}
