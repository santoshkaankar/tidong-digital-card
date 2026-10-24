<?php

// Only deployable, reviewed modules may be listed here. No PHP uploading or runtime installation.
return [
    'delivery' => [
        'name' => 'Delivery Partners', 'installed' => true,
        'default_enabled' => false,
        'paths' => ['delivery', 'delivery/*', 'vendor/delivery', 'vendor/delivery/*', 'admin/delivery', 'admin/delivery/*', 'staff-admin/delivery', 'staff-admin/delivery/*', 'employee/delivery', 'employee/delivery/*'],
    ],
    'pos' => [
        'name' => 'Restaurant POS / QR Ordering', 'installed' => true,
        'default_enabled' => true,
        'paths' => ['pos', 'pos/*', 'menu/*'],
    ],
    'kds' => [
        'name' => 'Kitchen Display System', 'installed' => true,
        'default_enabled' => true,
        'paths' => ['kds', 'kds/*'],
    ],
    'sublimation' => [
        'name' => 'Sublimation Studio', 'installed' => true,
        'default_enabled' => true,
        'paths' => ['vendor/businesses/*/sublimation', 'vendor/businesses/*/sublimation/*'],
    ],
'game' => ['name' => 'Virtual Life V10 (private preview)', 'installed' => true, 'default_enabled' => false, 'paths' => ['game', 'game/*']],
    'marriage_planner' => [
        'name' => 'Marriage Planner (placeholder)', 'installed' => false,
        'default_enabled' => false,
        'paths' => ['marriage-planner', 'marriage-planner/*'],
    ],
    'partner_approvals' => [
        'name' => 'Professional / Service Provider Application Reviews',
        'installed' => true, 'default_enabled' => false,
        'paths' => [],
    ],
    'public_registration' => [
        'name' => 'Public Account Registration',
        'installed' => true, 'default_enabled' => false,
        'paths' => ['register'],
    ],
    'customer' => ['name' => 'Customer Marketplace', 'installed' => true, 'default_enabled' => true, 'paths' => []],
    'grocery' => ['name' => 'Grocery and Kirana', 'installed' => true, 'default_enabled' => false, 'paths' => []],
    'legal_advisory' => ['name' => 'Legal and Professional Advisory', 'installed' => true, 'default_enabled' => false, 'paths' => []],
    'affiliate' => ['name' => 'Affiliate Network (review)', 'installed' => true, 'default_enabled' => false, 'paths' => []],
    'billing' => ['name' => 'Billing Invoice Foundation (draft only)', 'installed' => false, 'default_enabled' => false, 'paths' => []],
    'ai_restaurant_assistant' => [
        'name' => 'TIDONG AI – Restaurant Voice Assistant (read-only beta)',
        'installed' => true, 'default_enabled' => false,
        'paths' => [],
    ],
    'ai_public_help' => ['name' => 'AI Public Help Corpus (draft only)', 'installed' => false, 'default_enabled' => false, 'paths' => []],
];
