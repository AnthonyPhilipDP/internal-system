<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\PriceQuoteEquipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuote extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function equipment_list() {
        return $this->hasMany(PriceQuoteEquipment::class);
    }

}
