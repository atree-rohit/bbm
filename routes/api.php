<?php

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

Route::get('/taxa', function(){
    return Taxa::all()->toJson();
});

Route::get('/users', function(){
    return User::all()->toJson();
});

Route::get('/observations', function(){
    return [
        "count" => CountForm::with("rows")->get()->toArray(),
        "inat" => InatObservation::all()->toArray(),
        "ibp" => IbpObservation::all()->toArray(),
        "ifb" => IfbObservation::all()->toArray()
    ];
});