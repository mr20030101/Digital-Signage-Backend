<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Superadmin sees all content, regular users see only their own
        if ($user->isSuperAdmin()) {
            $contents = \App\Models\Content::all();
        } else {
            $contents = \App\Models\Content::where('user_id', $user->id)->get();
        }
        
        return response()->json($contents);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:image,video,webpage,html',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm,mov|max:51200',
            'content' => 'nullable|string',
            'duration' => 'required|integer|min:1',
        ]);

        $filePath = null;
        $thumbnailPath = null;
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('content', 'public');
            
            // Generate thumbnail for videos
            if ($validated['type'] === 'video') {
                try {
                    $fullPath = storage_path('app/public/' . $filePath);
                    $thumbnailName = pathinfo($filePath, PATHINFO_FILENAME) . '_thumb.jpg';
                    $thumbnailPath = 'content/thumbnails/' . $thumbnailName;
                    $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);
                    
                    // Create thumbnails directory if it doesn't exist
                    if (!file_exists(dirname($thumbnailFullPath))) {
                        mkdir(dirname($thumbnailFullPath), 0755, true);
                    }
                    
                    // Generate thumbnail using FFmpeg
                    \FFMpeg\FFMpeg::create([
                        'ffmpeg.binaries'  => '/opt/homebrew/bin/ffmpeg',
                        'ffprobe.binaries' => '/opt/homebrew/bin/ffprobe',
                    ])
                    ->open($fullPath)
                    ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                    ->save($thumbnailFullPath);
                } catch (\Exception $e) {
                    \Log::error('Thumbnail generation failed: ' . $e->getMessage());
                    $thumbnailPath = null;
                }
            }
        }

        $content = \App\Models\Content::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'content' => $validated['content'] ?? null,
            'duration' => $validated['duration'],
            'user_id' => $request->user()->id,
        ]);

        return response()->json($content, 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $content = \App\Models\Content::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $content->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($content);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $content = \App\Models\Content::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $content->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:image,video,webpage,html',
            'file_path' => 'nullable|string',
            'content' => 'nullable|string',
            'duration' => 'sometimes|integer|min:1',
        ]);

        $content->update($validated);
        return response()->json($content);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $content = \App\Models\Content::findOrFail($id);
        
        // Check ownership for regular users
        if (!$user->isSuperAdmin() && $content->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $content->delete();
        return response()->json(null, 204);
    }
}
