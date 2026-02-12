<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\RankingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'message' => 'Server is running']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/curriculum', [LessonController::class, 'curriculum']);
    Route::get('/levels/{id}', [LessonController::class, 'show']);
    Route::get('/levels/{id}', [LessonController::class, 'show']);
    Route::post('/lesson/complete', [LessonController::class, 'complete']);
    Route::post('/user/progress', [LessonController::class, 'updateProgress']); // Keep existing if needed
    Route::post('/user/stats', [\App\Http\Controllers\API\ProgressController::class, 'updateStats']);
    Route::post('/training/complete', [\App\Http\Controllers\API\TrainingController::class, 'complete']);
    Route::get('/rankings', [RankingController::class, 'index']);
    Route::get('/notes', [\App\Http\Controllers\NoteAudioController::class, 'index']);
    Route::post('/gamification/deduct-life', [\App\Http\Controllers\API\GamificationController::class, 'deductLife']);
});
