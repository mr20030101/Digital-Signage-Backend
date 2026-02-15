<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Superadmin sees all displays, regular users see only their own
        if ($user->isSuperAdmin()) {
            $displays = \App\Models\Display::with('layout')->get();
        } else {
            $displays = \App\Models\Display::where('user_id', $user->id)->with('layout')->get();
        }
        
        return response()->json($displays);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        // Generate unique display code
        $code = strtoupper(substr(md5(uniqid()), 0, 6));

        $display = \App\Models\Display::create([
            'name' => $validated['name'],
            'code' => $code,
            'location' => $validated['location'] ?? null,
            'user_id' => $request->user()->id,
            'status' => 'offline',
        ]);

        return response()->json([
            'display' => $display,
            'code' => $code,
            'message' => 'Display created successfully. Use this code in the player.',
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $display = \App\Models\Display::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($display);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $display = \App\Models\Display::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|in:online,offline',
            'layout_id' => 'nullable|exists:layouts,id',
        ]);

        $display->update($validated);
        return response()->json($display);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $display = \App\Models\Display::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $display->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $display->delete();
        return response()->json(null, 204);
    }
}
