<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/audio-generator', [App\Http\Controllers\NoteAudioController::class, 'generator']);
Route::post('/audio-generator/upload', [App\Http\Controllers\NoteAudioController::class, 'store']);
