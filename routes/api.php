<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\FingerprintTemplateController;
use App\Http\Controllers\LogKehadiranController;

Route::get('/fingerprint-templates', [FingerprintTemplateController::class, 'getAllHexData']);
Route::middleware('api')->post('/fingerprint', [FingerprintController::class, 'store']);
Route::post('/log-kehadiran', [LogKehadiranController::class, 'logKehadiran']);
