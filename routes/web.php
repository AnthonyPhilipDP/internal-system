<?php

use App\Livewire\Index;
use App\Livewire\Certificate;
use App\Livewire\ReleaseNotes;
use App\Livewire\EquipmentLabel;
use Illuminate\Support\Facades\Route;
use App\Livewire\AcknowledgmentReceipt;

Route::get('/', Index::class);
Route::get('/release-notes', ReleaseNotes::class);
Route::get('/equipment/print-label', EquipmentLabel::class);
Route::get('/acknowledgment-receipt', AcknowledgmentReceipt::class);
Route::get('/equipment/certificate', Certificate::class);