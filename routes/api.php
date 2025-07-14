<?php

use App\Http\Controllers\Api\TextController;
use App\OpenApi\Controllers\SwaggerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/user', fn(Request $request) => $request->user());

require __DIR__.'/auth.php';

// API Documentation
Route::get('/openapi.json', [SwaggerController::class, 'json']);

Route::middleware('auth:sanctum')
    ->get('/texts', [TextController::class, 'index']);
