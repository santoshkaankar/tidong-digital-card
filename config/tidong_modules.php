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
    'game' => [
        'name' => 'Virtual City Game (placeholder)', 'installed' => false,
        'default_enabled' => false,
        'paths' => ['game', 'game/*'],
    ],
    'marriage_planner' => [
        'name' => 'Marriage Planner (placeholder)', 'installed' => false,
        'default_enabled' => false,
        'paths' => ['marriage-planner', 'marriage-planner/*'],
    ],
];
