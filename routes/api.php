<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/curriculum', [LessonController::class, 'curriculum']);
    Route::get('/levels/{id}', [LessonController::class, 'show']);
    Route::post('/lesson/complete', [LessonController::class, 'complete']);
});
