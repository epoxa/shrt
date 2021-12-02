<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/test', [UrlController::class, 'test']);
Route::get('/', [UrlController::class, 'create'])->name('create');
Route::get('/save', [UrlController::class, 'save']);
Route::get('/stat', [UrlController::class, 'globalStat']);
Route::get('/{url}', [UrlController::class, 'click']);
Route::get('/{url}/stat', [UrlController::class, 'stat'])->name('stat');
