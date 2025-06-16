<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded =[
        'id'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'less_percentage' => 'decimal:2',
        'less_amount' => 'decimal:2',
        'charge_percentage' => 'decimal:2',
        'charge_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
