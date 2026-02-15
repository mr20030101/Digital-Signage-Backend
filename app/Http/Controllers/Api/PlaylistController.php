<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Superadmin sees all playlists, regular users see only their own
        if ($user->isSuperAdmin()) {
            $playlists = \App\Models\Playlist::with('contents')->get();
        } else {
            $playlists = \App\Models\Playlist::where('user_id', $user->id)->with('contents')->get();
        }
        
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
            'user_id' => $request->user()->id,
        ]);

        return response()->json($playlist, 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::with('contents')->findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($playlist);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $playlist->update($validated);
        return response()->json($playlist);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $playlist->delete();
        return response()->json(null, 204);
    }

    // Add content to playlist
    public function addContent(Request $request, string $id)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'content_id' => 'required|exists:contents,id',
            'order' => 'nullable|integer',
        ]);

        // Get the next order number if not provided
        if (!isset($validated['order'])) {
            $maxOrder = $playlist->contents()->max('order');
            $validated['order'] = $maxOrder !== null ? $maxOrder + 1 : 0;
        }

        // Check if content is already in playlist
        if ($playlist->contents()->where('content_id', $validated['content_id'])->exists()) {
            return response()->json(['message' => 'Content already in playlist'], 400);
        }

        $playlist->contents()->attach($validated['content_id'], ['order' => $validated['order']]);
        
        return response()->json($playlist->load('contents'));
    }

    // Remove content from playlist
    public function removeContent(Request $request, string $playlistId, string $contentId)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::findOrFail($playlistId);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $playlist->contents()->detach($contentId);
        
        return response()->json($playlist->load('contents'));
    }

    // Reorder playlist contents
    public function reorderContents(Request $request, string $id)
    {
        $user = $request->user();
        $playlist = \App\Models\Playlist::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $playlist->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'contents' => 'required|array',
            'contents.*.id' => 'required|exists:contents,id',
            'contents.*.order' => 'required|integer',
        ]);

        foreach ($validated['contents'] as $content) {
            $playlist->contents()->updateExistingPivot($content['id'], ['order' => $content['order']]);
        }

        return response()->json($playlist->load('contents'));
    }
}
