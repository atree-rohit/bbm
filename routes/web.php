<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IBPController;
use App\Http\Controllers\INatController;
use App\Http\Controllers\HexMapController;
use App\Http\Controllers\FormRowController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\AnalysisController;
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
Route::get('/butterfly_count/deg2dec', [CountFormController::class, 'deg2dec']);
Route::post('/butterfly_count/deg2dec_update', [CountFormController::class, 'deg2dec']);

Route::get('/species/id_quality', [FormRowController::class, 'id_quality']);
Route::post('/species/id_quality_update', [FormRowController::class, 'id_quality_update']);
Route::get('/species/correct', [FormRowController::class, 'correct']);
Route::get('/species/common2sci', [FormRowController::class, 'common2sci']);
Route::post('/species/correct_update', [FormRowController::class, 'correct_update']);

Route::get('/analysis/summary', [AnalysisController::class, 'summary']);
Route::get('/analysis/summary_people', [AnalysisController::class, 'summary_people']);
Route::get('/analysis/families', [AnalysisController::class, 'families']);
Route::resource('/analysis', AnalysisController::class);
Route::get('/map', [HexMapController::class, 'index']);

Route::resource('/butterfly_count', CountFormController::class);
Route::resource('/species', FormRowController::class);
Route::resource('/species_names', SpeciesController::class);
Route::resource('/ibp', IBPController::class);
Route::resource('/inat', INatController::class);
