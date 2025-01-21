<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'equipment_id',
        'name',
        'quantity'
    ];

    public function equipment(){
        return $this->belongsTo(Equipment::class);
    }

    public function acceDescription(): string
    {
        return $this->quantity.' '.$this->name;
    }
}
