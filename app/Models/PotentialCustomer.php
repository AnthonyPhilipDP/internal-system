<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PotentialCustomerContactPerson;

class PotentialCustomer extends Model
{
    use SoftDeletes;
    
    protected $guarded = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->customer_id = static::withTrashed()->max('customer_id') + 1;
        });
    }

    public function contactPerson() {
        return $this->hasMany(PotentialCustomerContactPerson::class);
    }

    public function activeContactPerson() {
        return $this->hasMany(PotentialCustomerContactPerson::class)->where('isActive', true);
    }
}
