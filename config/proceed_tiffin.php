<?php
// Only the legacy Proceed Tiffin workflow: no subscription, billing or automatic Thali catalog.
return [
    'timezone' => 'Asia/Kolkata',
    'slots' => ['breakfast'=>'Breakfast','lunch'=>'Lunch','snacks'=>'Evening Snacks','dinner'=>'Dinner'],
    'scopes' => ['day'=>'Day — Tiffin','everyday'=>'Everyday — Thali'],
];