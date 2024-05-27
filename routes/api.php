<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobOffersController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/job-offers', [JobOffersController::class, 'store']);

Route::get('/test', [JobOffersController::class, 'test']);

