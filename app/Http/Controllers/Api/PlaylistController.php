<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index()
    {
        $playlists = \App\Models\Playlist::with('contents')->get();
        return response()->json($playlists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $playlist = \App\Models\Playlist::create([
            ...$validated,
            'user_id' => 1,
        ]);

        return response()->json($playlist, 201);
    }

    public function show(string $id)
    {
        $playlist = \App\Models\Playlist::with('contents')->findOrFail($id);
        return response()->json($playlist);
    }

    public function update(Request $request, string $id)
    {
        $playlist = \App\Models\Playlist::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $playlist->update($validated);
        return response()->json($playlist);
    }

    public function destroy(string $id)
    {
        $playlist = \App\Models\Playlist::findOrFail($id);
        $playlist->delete();
        return response()->json(null, 204);
    }
}
