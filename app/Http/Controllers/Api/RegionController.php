<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = \App\Models\Region::all();
        return response()->json($regions);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'layout_id' => 'required|exists:layouts,id',
            'name' => 'required|string|max:255',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'top' => 'required|integer|min:0',
            'left' => 'required|integer|min:0',
            'z_index' => 'required|integer',
            'playlist_id' => 'nullable|exists:playlists,id',
            'content_id' => 'nullable|exists:contents,id',
        ]);

        // Verify the layout belongs to the user (unless superadmin)
        if (!$user->isSuperAdmin()) {
            $layout = \App\Models\Layout::findOrFail($validated['layout_id']);
            if ($layout->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized - Layout does not belong to you'], 403);
            }
        }

        $region = \App\Models\Region::create($validated);
        return response()->json($region, 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $region = \App\Models\Region::findOrFail($id);
        
        // Check ownership for regular users (region belongs to user if layout belongs to user)
        if (!$user->isSuperAdmin() && $region->layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($region);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $region = \App\Models\Region::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $region->layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'width' => 'sometimes|integer|min:1',
            'height' => 'sometimes|integer|min:1',
            'top' => 'sometimes|integer|min:0',
            'left' => 'sometimes|integer|min:0',
            'z_index' => 'sometimes|integer',
            'playlist_id' => 'nullable|exists:playlists,id',
            'content_id' => 'nullable|exists:contents,id',
        ]);

        $region->update($validated);
        return response()->json($region);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $region = \App\Models\Region::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $region->layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $region->delete();
        return response()->json(null, 204);
    }
}
