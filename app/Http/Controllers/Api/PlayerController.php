<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10',
            'name' => 'nullable|string|max:255',
            'resolution' => 'nullable|string|max:50',
        ]);

        // Find display by code
        $display = \App\Models\Display::where('code', $validated['code'])->first();
        
        if (!$display) {
            \Log::warning('Display registration attempt - code not found', [
                'code' => $validated['code'],
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'error' => 'Display not found',
                'message' => 'No display found with this code. Please verify the code in the CMS.',
                'code' => $validated['code'],
            ], 404);
        }

        // Update the display status and IP
        $display->update([
            'name' => $validated['name'] ?? $display->name,
            'ip_address' => $request->ip(), // Track current IP
            'status' => 'online',
            'last_seen' => now(),
        ]);

        return response()->json([
            'display' => $display,
            'message' => 'Display connected successfully',
        ]);
    }

    public function getContent(Request $request, $code)
    {
        // Get the authenticated user from middleware
        $playerUser = $request->attributes->get('player_user');
        
        $display = \App\Models\Display::where('code', $code)
            ->where('user_id', $playerUser->id) // Only show displays belonging to this user
            ->with(['layout.regions.content', 'layout.regions.playlist.contents', 'schedules'])
            ->first();
        
        if (!$display) {
            return response()->json([
                'error' => 'Display not found',
                'message' => 'This display code is not registered or does not belong to your account.',
            ], 404);
        }
        
        // Update status and IP
        $display->update([
            'ip_address' => $request->ip(),
            'status' => 'online',
            'last_seen' => now(),
        ]);

        // Check for active schedules first
        // Use user's timezone for schedule checking
        $now = \Carbon\Carbon::now($playerUser->timezone);
        
        \Log::info('Checking schedules', [
            'user_id' => $playerUser->id,
            'user_timezone' => $playerUser->timezone,
            'current_time' => $now->toDateTimeString(),
            'current_time_format' => $now->format('Y-m-d H:i:s'),
        ]);
        
        // Get all schedules for debugging
        $allSchedules = $display->schedules()
            ->where('is_active', true)
            ->get();
            
        \Log::info('All active schedules', [
            'count' => $allSchedules->count(),
            'schedules' => $allSchedules->map(function($s) use ($now) {
                return [
                    'id' => $s->id,
                    'start' => $s->start_time,
                    'end' => $s->end_time,
                    'start_check' => $s->start_time <= $now ? 'PASS' : 'FAIL',
                    'end_check' => $s->end_time >= $now ? 'PASS' : 'FAIL',
                ];
            })->toArray(),
        ]);
        
        // Convert to string format for database comparison
        $nowString = $now->format('Y-m-d H:i:s');
        
        $activeSchedule = $display->schedules()
            ->where('is_active', true)
            ->where('start_time', '<=', $nowString)
            ->where('end_time', '>=', $nowString)
            ->with(['playlist.contents', 'layout.regions.content', 'layout.regions.playlist.contents'])
            ->first();

        // If there's an active schedule, return the scheduled content
        if ($activeSchedule) {
            // Check if schedule has a playlist
            if ($activeSchedule->playlist) {
                \Log::info('Active schedule found with playlist', [
                    'schedule_id' => $activeSchedule->id,
                    'playlist' => $activeSchedule->playlist->name,
                    'start' => $activeSchedule->start_time,
                    'end' => $activeSchedule->end_time,
                ]);
                
                return response()->json([
                    'type' => 'schedule',
                    'schedule' => $activeSchedule,
                    'playlist' => $activeSchedule->playlist,
                ]);
            }
            // Check if schedule has a layout
            elseif ($activeSchedule->layout) {
                \Log::info('Active schedule found with layout', [
                    'schedule_id' => $activeSchedule->id,
                    'layout' => $activeSchedule->layout->name,
                    'start' => $activeSchedule->start_time,
                    'end' => $activeSchedule->end_time,
                ]);
                
                return response()->json([
                    'type' => 'schedule_layout',
                    'schedule' => $activeSchedule,
                    'layout' => $activeSchedule->layout,
                ]);
            }
        }

        \Log::info('No active schedule, returning layout');

        // Otherwise, return the default layout
        if (!$display->layout) {
            return response()->json(['message' => 'No layout assigned'], 200);
        }

        return response()->json([
            'type' => 'layout',
            'layout' => $display->layout,
        ]);
    }
}
