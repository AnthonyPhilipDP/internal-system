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
