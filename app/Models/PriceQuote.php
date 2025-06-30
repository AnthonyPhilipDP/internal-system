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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($price_quote) {
            // Only set if not already set
            if (empty($price_quote->price_quote_number)) {
                do {
                    $max = self::max('price_quote_number');
                    $next = $max ? $max + 1 : 18900;
                    $exists = self::where('price_quote_number', $next)->exists();
                } while ($exists);

                $price_quote->price_quote_number = $next;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function equipment_list() {
        return $this->hasMany(PriceQuoteEquipment::class);
    }

}
