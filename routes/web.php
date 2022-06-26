<?php

use App\Http\Controllers\HealthCheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('health-check')->name('health-check.')->group(function () {
    Route::get('liveness', [HealthCheckController::class, 'liveness'])->name('liveness');
    Route::get('readiness', [HealthCheckController::class, 'readiness'])->name('readiness');
});
