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

        $region = \App\Models\Region::create($validated);
        return response()->json($region, 201);
    }

    public function show(string $id)
    {
        $region = \App\Models\Region::findOrFail($id);
        return response()->json($region);
    }

    public function update(Request $request, string $id)
    {
        $region = \App\Models\Region::findOrFail($id);
        
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

    public function destroy(string $id)
    {
        $region = \App\Models\Region::findOrFail($id);
        $region->delete();
        return response()->json(null, 204);
    }
}
