<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\ContactPerson;
use App\Models\DeliveryPerson;
use App\Models\ClientExclusive;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->customer_id = static::withTrashed()->max('customer_id') + 1;
        });
    }

    public function getDisplayDateAttribute()
    {
        // If created_at is not null, format and return it
        if ($this->created_at) {
            return $this->created_at->format('F d, Y');
        }

        if ($this->createdDate) {
            try {
                return Carbon::parse($this->createdDate)->format('F d, Y');
            } catch (\Exception $e) {
                return 'Imported';
            }
        }
    }

     public function getCreatedDateAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return 'N/A';
        }
    
        try {
            // Use Carbon to format the date
            $date = Carbon::parse($value);
            return $date->format('F j, Y'); // Example format: January 1, 2023
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            return 'N/A';
        }
    }

    // equipment.customer_id matches customers.customer_id (not id)
    public function equipment(){
        return $this->hasMany(Equipment::class, 'customer_id', 'customer_id');
    }

    public function exclusive(){
        return $this->hasMany(ClientExclusive::class, 'customer_id', 'customer_id');
    }

    public function contactPerson() {
        return $this->hasMany(ContactPerson::class, 'customer_id', 'customer_id');
    }

    public function deliveryPerson() {
        return $this->hasMany(DeliveryPerson::class);
    }

    public function activeContactPerson() {
        return $this->hasMany(ContactPerson::class, 'customer_id', 'customer_id')->where('isActive', true);
    }

    protected $casts = [
        'vatExempt' => 'boolean',
        'othersForVat' => 'boolean',
        'othersForPayment' => 'boolean',
    ];

    public function getTelephonesAttribute()
    // This will be an array
    // Example usage:
    // $customer->telephones['telephone1'];
    // $customer->telephones['telephone2'];
    {
        if (!empty($this->telephone1) || !empty($this->telephone2)) {
            return [
                
                'telephone1' => $this->telephone1,
                'telephone2' => $this->telephone2,
            ];
        } else {
            return [
                'telephone1' => $this->old_telephone,
                'telephone2' => $this->old_fax,
            ];
        }
    }
}
