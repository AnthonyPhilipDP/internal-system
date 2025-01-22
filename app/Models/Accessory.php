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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (is_null($model->name)) {
                $model->name = 'N/A';
            }

            if (is_null($model->quantity)) {
                $model->quantity = 'N/A';
            }
        });
    }
}
