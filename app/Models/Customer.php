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

    public function getFormattedMobileAttribute()
    {
        $mobile1 = $this->attributes['mobile1'];
        $mobile2 = $this->attributes['mobile2'];

        if (is_null($mobile1) || $mobile1 === '') {
            return 'N/A';
        }

        // Format mobile numbers (example format: (0991) 234-5678)
        $formattedMobile1 = sprintf('(%s) %s-%s',
            substr($mobile1, 0, 4),
            substr($mobile1, 4, 3),
            substr($mobile1, 7)
        );

        $formattedMobile = $formattedMobile1;
        if (!is_null($mobile2) && $mobile2 !== '') {
            $formattedMobile2 = sprintf('(%s) %s-%s',
                substr($mobile2, 0, 4),
                substr($mobile2, 4, 3),
                substr($mobile2, 7)
            );
            $formattedMobile .= "<br>" . $formattedMobile2;
        }

        return $formattedMobile;
    }

    public function getFormattedTelephoneAttribute()
    {
        $telephone1 = $this->attributes['telephone1'];
        $telephone2 = $this->attributes['telephone2'];
        $areaCodeTelephone1 = $this->attributes['areaCodeTelephone1'];
        $areaCodeTelephone2 = $this->attributes['areaCodeTelephone2'];

        if (is_null($telephone1) || $telephone1 === '') {
            return 'N/A';
        }

        // Format the first telephone number
        $formattedTelephone1 = $this->formatTelephone($areaCodeTelephone1, $telephone1);

        $formattedTelephones = $formattedTelephone1;
        if (!is_null($telephone2) && $telephone2 !== '') {
            // Format the second telephone number
            $formattedTelephone2 = $this->formatTelephone($areaCodeTelephone2, $telephone2);
            $formattedTelephones .= "<br>" . $formattedTelephone2;
        }

        return $formattedTelephones;
    }

    private function formatTelephone($areaCode, $telephone)
    {
        $length = strlen($telephone);

        // Check if the telephone number is not exactly 7 or 8 digits
        if ($length !== 7 && $length !== 8) {
            return $telephone; // Return the original text if not exactly 7 or 8 digits
        }

        // Format the telephone number with or without area code
        if ($areaCode) {
            if ($length === 7) {
                return sprintf('(%s) %s-%s',
                    $areaCode,
                    substr($telephone, 0, 3),
                    substr($telephone, 3)
                );
            } else { // length is 8
                return sprintf('(%s) %s-%s',
                    $areaCode,
                    substr($telephone, 0, 4),
                    substr($telephone, 4)
                );
            }
        } else {
            if ($length === 7) {
                return sprintf('%s-%s',
                    substr($telephone, 0, 3),
                    substr($telephone, 3)
                );
            } else { // length is 8
                return sprintf('%s-%s',
                    substr($telephone, 0, 4),
                    substr($telephone, 4)
                );
            }
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
        return $this->hasMany(ContactPerson::class);
    }

    public function deliveryPerson() {
        return $this->hasMany(DeliveryPerson::class);
    }

    public function activeContactPerson() {
        return $this->hasMany(ContactPerson::class)->where('isActive', true);
    }

    protected $casts = [
        'vatExempt' => 'boolean',
        'othersForVat' => 'boolean',
        'othersForPayment' => 'boolean',
    ];
}
