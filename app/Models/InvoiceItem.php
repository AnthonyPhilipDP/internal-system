<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $guarded =[
        'id'
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
