<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Player Security Settings
    |--------------------------------------------------------------------------
    */

    // IP Whitelist - Leave empty to allow all IPs
    // Example: ['192.168.1.100', '192.168.1.101', '10.0.0.0/24']
    'allowed_ips' => env('PLAYER_ALLOWED_IPS', ''),

    // Require display approval before allowing content access
    'require_approval' => env('PLAYER_REQUIRE_APPROVAL', false),

    // Maximum displays per IP address (0 = unlimited)
    'max_displays_per_ip' => env('PLAYER_MAX_DISPLAYS_PER_IP', 0),

    // Enable audit logging for player access
    'audit_logging' => env('PLAYER_AUDIT_LOGGING', true),

    // Token rotation interval in days (0 = manual only)
    'token_rotation_days' => env('PLAYER_TOKEN_ROTATION_DAYS', 0),
];
