<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'address',
        'phone',
        'landline',
        'email',
        'website',
        'sec',
        'vat',
        'why',
        'businessNature',
        'qualifyingSystem',
        'certifyingBody',
        'dateCertified',
        'payment',
        'status',
        'remarks',
        'businessStyle',
        'tin',
        'acct_created',
    ];

    public function equipment() {
        return $this->hasMany(Equipment::class);
    } 

}
