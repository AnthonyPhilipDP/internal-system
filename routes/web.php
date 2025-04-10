<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.welcome');
});

Route::get('/worksheet', function () {
    return view('pages.worksheet');
});

Route::get('/acknowledgment-receipt', function () {
    return view('pages.acknowledgment-receipt');
});

Route::get('/equipment/print-label', function () {
    return view('pages.equipment-label');
});

Route::get('/release-notes', function () {
    return view('pages.release-notes');
});