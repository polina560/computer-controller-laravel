<?php

use App\Http\Controllers\ComputerStatusController;
use App\MoonShine\Resources\ComputerLogResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('app'));

Route::get('/api/docs', fn() => view('swagger/ui', [
    'jsonUrl' => url('/api/openapi.json'),
]))->middleware('moonshine.basic');

Route::get('admin/computer-status-data', [ComputerStatusController::class, 'getData'])
    ->name('moonshine.computer.status.data');
