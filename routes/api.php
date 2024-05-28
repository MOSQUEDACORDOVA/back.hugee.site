<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobOffersController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/job-offers/{id}', [JobOffersController::class, 'update']);
    Route::delete('/job-offers/{id}', [JobOffersController::class, 'destroy']);

});

Route::post('/job-offers', [JobOffersController::class, 'store']);
Route::get('/job-offers', [JobOffersController::class, 'index']);


