<?php

namespace App\Models;

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
        'fax',
        'email',
        'site',
        'SEC',
        'VAT',
        'WTP',
        'main_act',
        'QS',
        'certifying_body',
        'date_certified',
        'payment',
        'status',
        'remarks',
        'business_system',
        'tin',
        'acct_created',
    ];
}
