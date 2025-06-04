<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NcfReport extends Model
{
    use softDeletes;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'specificFailure' => 'array',
        'correctiveAction' => 'array',
        'clientDecisionRecommendation' => 'array',
        'clientDecision' => 'array',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'transaction_id', 'transaction_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Change the status of the equipment when save using NcfReport
        static::saving(function ($report) {
            $equipment = Equipment::where('transaction_id', $report->transaction_id)->first();
            if ($equipment) {
                $equipment->update(['status' => $report->status]);
            }
        });
    }
}
