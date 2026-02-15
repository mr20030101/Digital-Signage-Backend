<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'global_player_token' => Setting::get('global_player_token'),
            'api_url' => url('/api'),
        ];
        
        return response()->json($settings);
    }

    public function regeneratePlayerToken(Request $request)
    {
        $newToken = Str::random(64);
        Setting::set('global_player_token', $newToken);
        
        return response()->json([
            'token' => $newToken,
            'message' => 'Global player token regenerated successfully. Update all players with this new token.',
        ]);
    }
}
