<?php

// Curated features ONLY. A switch is meaningful only where the backend checks it.
// Global technical availability must never grant access to a firm's business records.
return [
    'ai.restaurant_assistant' => [
        'name' => 'TIDONG AI – Restaurant Voice & Menu Discovery', 'module' => 'ai_restaurant_assistant', 'default_enabled' => false,
    ],
    'ai.professional_work_transparency' => [
        'name' => 'TIDONG AI – Professional Work Transparency (verified status only)', 'module' => 'ai_restaurant_assistant', 'default_enabled' => false,
    ],
    'delivery.partner_enrollment' => ['name' => 'Delivery Partner Enrollment', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.partner_approval' => ['name' => 'Vendor Partner Verification & Approval', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.vendor_dispatch' => ['name' => 'Vendor Delivery Dispatch', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.partner_progress' => ['name' => 'Partner Delivery Status Updates', 'module' => 'delivery', 'default_enabled' => true],
];
