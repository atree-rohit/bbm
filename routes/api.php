<?php

use App\Http\Controllers\ApiController;
use App\Models\Taxa;
use App\Models\User;
use App\Models\CountForm;
use Illuminate\Http\Request;
use App\Models\IbpObservation;
use App\Models\IfbObservation;
use App\Models\InatObservation;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/taxa', [ApiController::class, "get_taxa"]);

Route::get('/users', [ApiController::class, "get_users"]);

Route::get('/observations', [ApiController::class, "get_observations"]);
