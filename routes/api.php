<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisplayController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\PlaylistController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\AuthController;

// Auth routes (no auth required)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Player endpoints (no auth for now)
Route::post('/player/register', [PlayerController::class, 'register']);
Route::get('/player/{code}/content', [PlayerController::class, 'getContent']);

// Protected CMS API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::apiResource('displays', DisplayController::class);
    Route::apiResource('contents', ContentController::class);
    Route::apiResource('playlists', PlaylistController::class);
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('layouts', \App\Http\Controllers\Api\LayoutController::class);
    Route::apiResource('regions', \App\Http\Controllers\Api\RegionController::class);
});
