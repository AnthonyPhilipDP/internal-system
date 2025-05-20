<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Accessory;
use App\Models\Worksheet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'inspection' => 'array',
    ];
    
    protected $guarded = [
        'id',
    ];

    //equipment.customer_id matches customers.customer_id (not id)
    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function worksheet(){
        return $this->belongsTo(Worksheet::class);
    }

    public function accessory() {
        return $this->hasMany(Accessory::class);
    } 


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->transaction_id = static::withTrashed()->max('transaction_id') + 1;
            $model->inspection = $model->inspection ?? ['no visible damage'];
            $model->status = $model->status ?? 'incoming';
        });
    }

    public function getDecisionRuleName()
    {
        $decisionRuleNames = [
            'default' => 'Simple Calibration',
            'rule1' => 'Binary Statement for Simple Acceptance Rule ( w = 0 )',
            'rule2' => 'Binary Statement with Guard Band( w = U )',
            'rule3' => 'Non-binary Statement with Guard Band( w = U )',
        ];

        return $decisionRuleNames[$this->decisionRule] ?? '';
    }

    public function getValidationAttribute($value)
    {
        // Only pad if it's a single digit (0-9)
        if (is_numeric($value) && $value >= 0 && $value < 10) {
            return str_pad($value, 2, '0', STR_PAD_LEFT);
        }
        return $value;
    }
}
