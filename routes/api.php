<?php

use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Owner\PropertyController;
use App\Http\Controllers\Api\V1\User\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('auth/register', RegisterController::class);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('owner/properties', [PropertyController::class,'index']);
        Route::get('user/booking',[BookingController::class,'index']);
    });
});
