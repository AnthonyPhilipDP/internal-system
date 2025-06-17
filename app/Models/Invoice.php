<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [
        'id'
    ];
    
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            // Only set if not already set
            if (empty($invoice->invoice_number)) {
                do {
                    $max = self::max('invoice_number');
                    $next = $max ? $max + 1 : 4338;
                    $exists = self::where('invoice_number', $next)->exists();
                } while ($exists);

                $invoice->invoice_number = $next;
            }
        });
    }
}
