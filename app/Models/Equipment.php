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

    public function customer(){
        return $this->belongsTo(Customer::class);
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
}
