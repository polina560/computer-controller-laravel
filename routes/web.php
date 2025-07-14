<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('app'));

Route::get('/api/docs', fn() => view('swagger/ui', [
    'jsonUrl' => url('/api/openapi.json'),
]))->middleware('moonshine.basic');
