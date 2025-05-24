<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintTemplateController;

Route::middleware('api')->get('/fingerprint-template/{id}', [FingerprintTemplateController::class, 'getHexData']);
Route::middleware('api')->post('/fingerprint', [FingerprintController::class, 'store']);
