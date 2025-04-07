<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\ContactPerson;
use App\Models\DeliveryPerson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'nickname',
        'address',
        'mobile1',
        'mobile2',
        'telephone1',
        'telephone2',
        'email',
        'website',
        'sec',
        'vat',
        'wht',
        'businessNature',
        'qualifyingSystem',
        'certifyingBody',
        'dateCertified',
        'payment',
        'status',
        'remarks',
        'businessStyle',
        'tin',
        'createdDate',
    ];

    public function equipment() {
        return $this->hasMany(Equipment::class);
    } 

    protected static function boot()
    {
        parent::boot();

        // static::saving(function ($model) {
        //     if (is_null($model->telephone)) {
        //         $model->telephone = 'N/A';
        //     }

        //     if (is_null($model->website)) {
        //         $model->website = 'N/A';
        //     }

        //     if (is_null($model->sec)) {
        //         $model->sec = 'N/A';
        //     }

        //     if (is_null($model->wht)) {
        //         $model->wht = 'N/A';
        //     }

        //     if (is_null($model->qualifyingSystem)) {
        //         $model->qualifyingSystem = 'N/A';
        //     }

        //     if (is_null($model->remarks)) {
        //         $model->remarks = 'N/A';
        //     }
        // });
    }

    public function getFormattedWebsiteAttribute()
    {
        $website = $this->attributes['website'];

        if (is_null($website) || $website === '') {
            return 'N/A';
        }

        return $website;
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

    public function getWebsiteAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getSecAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getWhtAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getQualifyingSystemAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getCertifyingBodyAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getRemarksAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getEmailAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }

    public function getStatusAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }
    
    public function getTinAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }
    
    public function getBusinessStyleAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }
    
    public function getDateCertifiedAttribute($value)
    {
        return $this->formatNullableAttribute($value);
    }
    
    public function getBusinessNatureAttribute($value)
    {
        return $this->formatNullableAttribute($value);
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

    // Helper method to format nullable attributes
    private function formatNullableAttribute($value)
    {
        return is_null($value) || $value === '' ? 'N/A' : $value;
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

        if (is_null($telephone1) || $telephone1 === '') {
            return 'N/A';
        }

        // Format telephone numbers (example format: (046) 430-1666)
        $formattedTelephone1 = sprintf('(%s) %s-%s',
            substr($telephone1, 0, 3),
            substr($telephone1, 3, 3),
            substr($telephone1, 6)
        );

        $formattedTelephones = $formattedTelephone1;
        if (!is_null($telephone2) && $telephone2 !== '') {
            $formattedTelephone2 = sprintf('(%s) %s-%s',
                substr($telephone2, 0, 3),
                substr($telephone2, 3, 3),
                substr($telephone2, 6)
            );
            $formattedTelephones .= "<br>" . $formattedTelephone2;
        }

        return $formattedTelephones;
    }


    public function contactPerson() {
        return $this->hasMany(ContactPerson::class);
    }

    public function deliveryPerson() {
        return $this->hasMany(DeliveryPerson::class);
    }

    public function activeContactPerson() {
        return $this->hasMany(ContactPerson::class)->where('is_active', true);
    }
}
