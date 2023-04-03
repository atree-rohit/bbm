<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DataCleanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'home']);
Route::get('/home', [HomeController::class, 'home']);
Route::get('/about', [HomeController::class, 'home']);
Route::get('/faq', [HomeController::class, 'home']);
Route::get('/videos', [HomeController::class, 'home']);
Route::get('/past_results', [HomeController::class, 'home']);
Route::get('/results', [HomeController::class, 'home']);
Route::get('/partners', [HomeController::class, 'home']);


Route::get('/import_data', [ImportController::class, 'import']);
Route::get('/clean', [DataCleanController::class, 'clean']);