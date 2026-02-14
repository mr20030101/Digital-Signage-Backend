<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $display = \App\Models\Display::create([
            'name' => $validated['name'],
            'code' => \Illuminate\Support\Str::random(10),
            'location' => $validated['location'] ?? null,
            'user_id' => 1, // Default user for now
            'status' => 'online',
            'last_seen' => now(),
        ]);

        return response()->json($display);
    }

    public function getContent(Request $request, $code)
    {
        $display = \App\Models\Display::where('code', $code)->firstOrFail();
        
        $display->update([
            'status' => 'online',
            'last_seen' => now(),
        ]);

        $schedule = \App\Models\Schedule::where('display_id', $display->id)
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->with('playlist.contents')
            ->first();

        if (!$schedule) {
            return response()->json(['message' => 'No active schedule'], 404);
        }

        return response()->json([
            'playlist' => $schedule->playlist,
            'contents' => $schedule->playlist->contents,
        ]);
    }
}
