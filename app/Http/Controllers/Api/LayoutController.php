<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        $layouts = \App\Models\Layout::with('regions')->get();
        return response()->json($layouts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'width' => 'required|integer',
            'height' => 'required|integer',
            'background_color' => 'nullable|string',
            'background_image' => 'nullable|string',
        ]);

        $layout = \App\Models\Layout::create([
            ...$validated,
            'user_id' => 1,
        ]);

        return response()->json($layout, 201);
    }

    public function show(string $id)
    {
        $layout = \App\Models\Layout::with('regions')->findOrFail($id);
        return response()->json($layout);
    }

    public function update(Request $request, string $id)
    {
        $layout = \App\Models\Layout::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'width' => 'sometimes|integer',
            'height' => 'sometimes|integer',
            'background_color' => 'nullable|string',
            'background_image' => 'nullable|string',
        ]);

        $layout->update($validated);
        return response()->json($layout);
    }

    public function destroy(string $id)
    {
        $layout = \App\Models\Layout::findOrFail($id);
        $layout->delete();
        return response()->json(null, 204);
    }
}
