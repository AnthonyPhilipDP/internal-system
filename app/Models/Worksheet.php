<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worksheet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'file',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($capability) {
            if ($capability->file) {
                Storage::disk('public')->delete($capability->file);
            }
        });
    }
}
