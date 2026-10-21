<?php

return [
    // Public signup MUST remain closed until explicitly approved after security review.
    // Existing permitted customer accounts may still sign in.
    'public_registration_enabled' => env('TIDONG_CUSTOMER_PUBLIC_REGISTRATION', false),
    'enabled' => env('TIDONG_CUSTOMER_MODULE_ENABLED', true),
    'max_addresses' => 10,
    'supported_locales' => ['en', 'hi'],
];
