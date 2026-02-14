<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = \App\Models\Schedule::with(['display', 'playlist'])->get();
        return response()->json($schedules);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_id' => 'required|exists:displays,id',
            'playlist_id' => 'required|exists:playlists,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'days_of_week' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $schedule = \App\Models\Schedule::create($validated);
        return response()->json($schedule, 201);
    }

    public function show(string $id)
    {
        $schedule = \App\Models\Schedule::with(['display', 'playlist'])->findOrFail($id);
        return response()->json($schedule);
    }

    public function update(Request $request, string $id)
    {
        $schedule = \App\Models\Schedule::findOrFail($id);
        
        $validated = $request->validate([
            'display_id' => 'sometimes|exists:displays,id',
            'playlist_id' => 'sometimes|exists:playlists,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'days_of_week' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);
        return response()->json($schedule);
    }

    public function destroy(string $id)
    {
        $schedule = \App\Models\Schedule::findOrFail($id);
        $schedule->delete();
        return response()->json(null, 204);
    }
}
