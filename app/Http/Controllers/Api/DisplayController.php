<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index()
    {
        $displays = \App\Models\Display::all();
        return response()->json($displays);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $display = \App\Models\Display::create([
            'name' => $validated['name'],
            'code' => \Illuminate\Support\Str::random(10),
            'location' => $validated['location'] ?? null,
            'user_id' => 1,
        ]);

        return response()->json($display, 201);
    }

    public function show(string $id)
    {
        $display = \App\Models\Display::findOrFail($id);
        return response()->json($display);
    }

    public function update(Request $request, string $id)
    {
        $display = \App\Models\Display::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:online,offline',
        ]);

        $display->update($validated);
        return response()->json($display);
    }

    public function destroy(string $id)
    {
        $display = \App\Models\Display::findOrFail($id);
        $display->delete();
        return response()->json(null, 204);
    }
}
