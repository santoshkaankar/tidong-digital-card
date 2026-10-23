<?php

return [
    // Rule 13: public registration is disabled until an authorized operator enables it.
    // Global ON/OFF is managed via the existing Admin Module Registry: public_registration.
    'terms_url' => env('TIDONG_TERMS_URL') ?: 'terms-and-conditions',
    'types' => [
        'vendor' => true,
        'delivery' => true,
        'professional' => true,
        'service_provider' => true,
        'customer' => true,
    ],
];
