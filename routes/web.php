<?php

use App\MoonShine\Resources\ComputerLogResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('app'));

Route::get('/api/docs', fn() => view('swagger/ui', [
    'jsonUrl' => url('/api/openapi.json'),
]))->middleware('moonshine.basic');

Route::prefix('admin')->middleware(['web', 'moonshine'])->group(function() {
    Route::get('/computer-logs/chart-data', function() {
        try {
            return response()->json(
                app(\App\MoonShine\Resources\ComputerLogResource::class)->getChartData()
            );
        } catch (\Exception $e) {
            Log::error('Chart data error: '.$e->getMessage());
            return response()->json([], 500);
        }
    });
});
