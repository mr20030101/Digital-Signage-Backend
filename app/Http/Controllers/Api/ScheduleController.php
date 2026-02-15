<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Superadmin sees all schedules, regular users see only schedules for their displays
        if ($user->isSuperAdmin()) {
            $schedules = \App\Models\Schedule::with(['display', 'playlist', 'layout'])->get();
        } else {
            // Get schedules where the display belongs to the user
            $schedules = \App\Models\Schedule::with(['display', 'playlist', 'layout'])
                ->whereHas('display', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->get();
        }
        
        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'display_id' => 'required|exists:displays,id',
            'playlist_id' => 'nullable|exists:playlists,id',
            'layout_id' => 'nullable|exists:layouts,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'days_of_week' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Verify the display belongs to the user (unless superadmin)
        if (!$user->isSuperAdmin()) {
            $display = \App\Models\Display::findOrFail($validated['display_id']);
            if ($display->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized - Display does not belong to you'], 403);
            }
            
            // Verify playlist belongs to user if provided
            if (isset($validated['playlist_id'])) {
                $playlist = \App\Models\Playlist::findOrFail($validated['playlist_id']);
                if ($playlist->user_id !== $user->id) {
                    return response()->json(['message' => 'Unauthorized - Playlist does not belong to you'], 403);
                }
            }
            
            // Verify layout belongs to user if provided
            if (isset($validated['layout_id'])) {
                $layout = \App\Models\Layout::findOrFail($validated['layout_id']);
                if ($layout->user_id !== $user->id) {
                    return response()->json(['message' => 'Unauthorized - Layout does not belong to you'], 403);
                }
            }
        }

        $schedule = \App\Models\Schedule::create($validated);
        return response()->json($schedule, 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $schedule = \App\Models\Schedule::with(['display', 'playlist', 'layout'])->findOrFail($id);
        
        // Check ownership for regular users (schedule belongs to user if display belongs to user)
        if (!$user->isSuperAdmin() && $schedule->display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($schedule);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $schedule = \App\Models\Schedule::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $schedule->display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'display_id' => 'sometimes|exists:displays,id',
            'playlist_id' => 'nullable|exists:playlists,id',
            'layout_id' => 'nullable|exists:layouts,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'days_of_week' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);
        return response()->json($schedule);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $schedule = \App\Models\Schedule::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $schedule->display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $schedule->delete();
        return response()->json(null, 204);
    }
}
