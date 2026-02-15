<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Superadmin sees all layouts, regular users see only their own
        if ($user->isSuperAdmin()) {
            $layouts = \App\Models\Layout::with('regions')->get();
        } else {
            $layouts = \App\Models\Layout::where('user_id', $user->id)->with('regions')->get();
        }
        
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
            'user_id' => $request->user()->id,
        ]);

        return response()->json($layout, 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $layout = \App\Models\Layout::with('regions')->findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($layout);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $layout = \App\Models\Layout::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
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

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $layout = \App\Models\Layout::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $layout->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $layout->delete();
        return response()->json(null, 204);
    }
}
