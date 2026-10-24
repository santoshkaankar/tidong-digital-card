<?php

// Server-side controls only. Never expose OPENAI_API_KEY to the browser.
return [
    'api_key' => env('OPENAI_API_KEY', ''),
    // Paid provider is opt-in even when an API key exists on the server.
    'paid_provider_enabled' => env('TIDONG_AI_PAID_PROVIDER_ENABLED', false),
    'model' => env('TIDONG_AI_RESTAURANT_MODEL', 'gpt-4.1-mini'),
    'daily_user_requests' => 20,
    'daily_platform_requests' => 250,
    'nearby_radius_km' => 12,
];
