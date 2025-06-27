<?php

namespace App\Models;

use App\Models\Equipment;
use App\Models\PriceQuote;
use Illuminate\Database\Eloquent\Model;

class PriceQuoteEquipment extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'vat' => 'boolean',
    ];

    public function price_quote() {
        return $this->belongsTo(PriceQuote::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
