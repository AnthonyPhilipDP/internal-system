<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Support\Str;
use App\Models\ContactPerson;
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
        'address',
        'mobile',
        'telephone',
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

        static::saving(function ($model) {
            if (is_null($model->telephone)) {
                $model->landline = 'N/A';
            }

            if (is_null($model->website)) {
                $model->website = 'N/A';
            }

            if (is_null($model->sec)) {
                $model->sec = 'N/A';
            }

            if (is_null($model->wht)) {
                $model->wht = 'N/A';
            }

            if (is_null($model->qualifyingSystem)) {
                $model->qualifyingSystem = 'N/A';
            }

            if (is_null($model->remarks)) {
                $model->remarks = 'N/A';
            }

            // if (is_null($model->createdDate)) {
            //     $model->createdDate = 'N/A';
            // }
        });
    }

    public function getDisplayDateAttribute()
    {
        return $this->created_at ?? $this->createdDate;
    }

    public function contactPerson() {
        return $this->hasMany(ContactPerson::class);
    }

    public function activeContactPerson() {
        return $this->hasMany(ContactPerson::class)->where('is_active', true);
    }
}
