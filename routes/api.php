<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RefluxController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/songplayed', [RefluxController::class, 'songplayed']);
Route::post('/addsong', [RefluxController::class, 'addsong']);
Route::post('/addchart', [RefluxController::class, 'addchart']);
Route::post('/postscore', [RefluxController::class, 'postscore']);
Route::post('/updatesong', [RefluxController::class, 'updatesong']);
Route::post('/unlocksong', [RefluxController::class, 'unlocksong']);

Route::get('/getscore', [RefluxController::class, 'getscore'])->middleware('auth:sanctum');
