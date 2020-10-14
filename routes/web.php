<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormRowController;
use App\Http\Controllers\CountFormController;

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

Route::get('/butterfly_count/import', [CountFormController::class, 'import']);
Route::resource('/butterfly_count', CountFormController::class);
Route::resource('/species', FormRowController::class);
