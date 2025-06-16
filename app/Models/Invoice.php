<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];
    
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
