<?php

use App\Http\Controllers\ComputerStatusController;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\DependencyInjection\MoonShine;

//Route::get('/computer-status-data', [ComputerStatusController::class, 'getData'])
//    ->name('moonshine.computer.status.data');

Route::prefix(MoonShine::path()) // Получает текущий префикс админки (по умолчанию '/admin')
    ->middleware(['web', 'moonshine'])
    ->group(function () {
        // Ваш маршрут для данных графика
        Route::get('/computer-status-data', [ComputerStatusController::class, 'getData'])
            ->name('computer.status.data');
    });
// Route::prefix(MoonShine::path())
//    ->middleware(['web', 'moonshine'])
//    ->group(function () {
//        // Ваши другие маршруты MoonShine...
//
//        Route::get('/computer-status-data', [ComputerStatusController::class, 'getData'])
//            ->name('computer.status.data');
//    });
