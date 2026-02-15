<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class ValidatePlayerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // IP Whitelist Check
        $allowedIPs = config('player.allowed_ips');
        if (!empty($allowedIPs)) {
            $allowedIPsArray = is_string($allowedIPs) ? explode(',', $allowedIPs) : $allowedIPs;
            $clientIP = $request->ip();
            
            $isAllowed = false;
            foreach ($allowedIPsArray as $allowedIP) {
                $allowedIP = trim($allowedIP);
                // Check for CIDR notation
                if (strpos($allowedIP, '/') !== false) {
                    if ($this->ipInRange($clientIP, $allowedIP)) {
                        $isAllowed = true;
                        break;
                    }
                } else {
                    if ($clientIP === $allowedIP) {
                        $isAllowed = true;
                        break;
                    }
                }
            }
            
            if (!$isAllowed) {
                \Log::warning('Player access denied - IP not whitelisted', [
                    'ip' => $clientIP,
                    'url' => $request->fullUrl(),
                ]);
                return response()->json([
                    'error' => 'Access denied',
                    'message' => 'Your IP address is not authorized to access this service.'
                ], 403);
            }
        }

        // Token Validation - Check against user tokens
        $token = $request->header('X-Player-Token');
        
        if (!$token) {
            \Log::warning('Player access denied - No token provided', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            return response()->json([
                'error' => 'No player token',
                'message' => 'Please configure your player with your user token from CMS Settings.'
            ], 401);
        }

        // Find user by player token
        $user = \App\Models\User::where('player_token', $token)->first();
        
        if (!$user) {
            \Log::warning('Player access denied - Invalid token', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            return response()->json([
                'error' => 'Invalid player token',
                'message' => 'Please configure your player with the correct token from CMS Settings.'
            ], 401);
        }

        // Attach user to request for later use
        $request->attributes->set('player_user', $user);

        // Audit Logging
        if (config('player.audit_logging', true)) {
            \Log::channel('daily')->info('Player API access', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);
        }

        return $next($request);
    }

    private function ipInRange($ip, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);
        $ip_long = ip2long($ip);
        $subnet_long = ip2long($subnet);
        $mask_long = -1 << (32 - $mask);
        $subnet_long &= $mask_long;
        return ($ip_long & $mask_long) == $subnet_long;
    }
}
