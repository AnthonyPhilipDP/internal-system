<?php

use App\Livewire\Index;
use App\Livewire\Certificate;
use App\Livewire\ReleaseNotes;
use App\Livewire\EquipmentLabel;
use App\Livewire\InvoiceManager;
use App\Livewire\CalibrationRecall;
use App\Livewire\NonConformityReport;
use Illuminate\Support\Facades\Route;
use App\Livewire\AcknowledgmentReceipt;

Route::get('/', Index::class);
Route::get('/release-notes', ReleaseNotes::class);
Route::get('/equipment/print-label', EquipmentLabel::class);
Route::get('/acknowledgment-receipt', AcknowledgmentReceipt::class);
Route::get('/equipment/certificate', Certificate::class);
Route::get('/calibration-recall', CalibrationRecall::class)->name('recallCalibration');
Route::get('/equipment/ncf-report/{reportId}', NonConformityReport::class)->name('ncfReport');
Route::get('/equipment/download-pdf/{reportId}', [NonConformityReport::class, 'downloadPdf'])->name('downloadPdf');
Route::get('/invoice-manager/{invoice_id}', InvoiceManager::class)->name('invoice-manager');