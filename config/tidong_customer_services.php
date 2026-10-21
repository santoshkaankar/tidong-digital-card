<?php

// Customer-facing directory only. This is not an ordering/checkout entitlement.
// A business is public only after vendor/business approval AND explicit ordering activation.
// Never add an external or vendor-admin route as a customer checkout shortcut.
return [
    'services' => [
        'sublimation' => [
            'title' => 'Sublimation & Gifts',
            'description' => 'Personalized mugs, photo gifts and more',
            'icon' => '🎁',
            'tone' => 'rose',
            'types' => ['sublimation'],
            'directory_enabled' => true,
        ],
        'restaurant' => [
            'title' => 'Restaurant & Food',
            'description' => 'Find restaurants and nearby kitchens',
            'icon' => '🍽️',
            'tone' => 'orange',
            'types' => ['restaurant', 'cafe'],
            'directory_enabled' => true,
        ],
        'grocery' => [
            'title' => 'Grocery',
            'description' => 'Daily essentials',
            'icon' => '🛒',
            'tone' => 'mint',
            'types' => [], // integrate once Grocery public-order rules are approved
            'directory_enabled' => false,
        ],
        'delivery' => [
            'title' => 'Delivery',
            'description' => 'Track deliveries in a future release',
            'icon' => '🚚',
            'tone' => 'blue',
            'types' => [],
            'directory_enabled' => false,
        ],
        'wallet' => [
            'title' => 'Wallet',
            'description' => 'Wallet integration coming later',
            'icon' => '👛',
            'tone' => 'purple',
            'types' => [],
            'directory_enabled' => false,
        ],
        'billing' => [
            'title' => 'Billing',
            'description' => 'Invoices integration coming later',
            'icon' => '🧾',
            'tone' => 'sky',
            'types' => [],
            'directory_enabled' => false,
        ],
    ],
];
