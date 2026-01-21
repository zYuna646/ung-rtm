<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RtmController;
use App\Http\Controllers\Api\RencanaTindakLanjutController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // RTM Routes
    Route::get('/rtm', [RtmController::class, 'index']);
    Route::get('/rtm/{id}', [RtmController::class, 'show']);
    
    // Fakultas & Prodi Routes
    Route::get('/fakultas', [RtmController::class, 'fakultas']);
    Route::get('/prodi', [RtmController::class, 'prodi']);
    Route::get('/fakultas/{fakultasId}/prodi', [RtmController::class, 'prodiByFakultas']);
    
    // RTM Lampiran & Report Routes
    Route::get('/rtm/{rtmId}/lampiran', [RtmController::class, 'lampiran']);
    Route::get('/rtm/{rtmId}/report', [RtmController::class, 'report']);
    
    // Data Source Routes
    Route::get('/akreditasi', [RtmController::class, 'akreditasi']);
    Route::get('/ami', [RtmController::class, 'ami']);
    Route::get('/survei', [RtmController::class, 'survei']);
    
    // Rencana Tindak Lanjut Routes
    Route::get('/rtm/{rtmId}/rencana-tindak-lanjut', [RencanaTindakLanjutController::class, 'index']);
    Route::post('/rtm/{rtmId}/rencana-tindak-lanjut', [RencanaTindakLanjutController::class, 'store']);
    Route::get('/rtm/{rtmId}/rencana-tindak-lanjut/{id}', [RencanaTindakLanjutController::class, 'show']);
    Route::put('/rtm/{rtmId}/rencana-tindak-lanjut/{id}', [RencanaTindakLanjutController::class, 'update']);
    Route::delete('/rtm/{rtmId}/rencana-tindak-lanjut/{id}', [RencanaTindakLanjutController::class, 'destroy']);
});
