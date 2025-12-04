<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecipientController;
use App\Http\Controllers\Api\GiftController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Recipients
    Route::apiResource('recipients', RecipientController::class);
    Route::get('/recipients/{id}/gifts', [RecipientController::class, 'gifts']);

    // Gifts
    Route::apiResource('gifts', GiftController::class);
});
