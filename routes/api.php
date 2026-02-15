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

// Player endpoints (require global player token)
Route::middleware(['player.token', 'throttle:60,1'])->group(function () {
    Route::post('/player/register', [PlayerController::class, 'register']);
    Route::get('/player/{code}/content', [PlayerController::class, 'getContent']);
});

// Protected CMS API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    Route::apiResource('displays', DisplayController::class);
    Route::apiResource('contents', ContentController::class);
    Route::apiResource('playlists', PlaylistController::class);
    
    // Playlist content management
    Route::post('/playlists/{id}/contents', [\App\Http\Controllers\Api\PlaylistController::class, 'addContent']);
    Route::delete('/playlists/{playlistId}/contents/{contentId}', [\App\Http\Controllers\Api\PlaylistController::class, 'removeContent']);
    Route::put('/playlists/{id}/contents/reorder', [\App\Http\Controllers\Api\PlaylistController::class, 'reorderContents']);
    
    Route::apiResource('schedules', ScheduleController::class);
    Route::apiResource('layouts', \App\Http\Controllers\Api\LayoutController::class);
    Route::apiResource('regions', \App\Http\Controllers\Api\RegionController::class);
    
    // Settings endpoints (admin only)
    Route::get('/settings', [\App\Http\Controllers\Api\SettingController::class, 'index']);
    Route::post('/settings/regenerate-player-token', [\App\Http\Controllers\Api\SettingController::class, 'regeneratePlayerToken']);
});
