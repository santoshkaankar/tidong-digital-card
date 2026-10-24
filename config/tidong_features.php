<?php

// Curated features ONLY. A switch is meaningful only where the backend checks it.
// Global technical availability must never grant access to a firm's business records.
return [
    'kds.free_assistant' => ['name'=>'KDS Free Assistant (no paid API)', 'module'=>'kds', 'default_enabled'=>false],
    'delivery.partner_enrollment' => ['name' => 'Delivery Partner Enrollment', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.partner_approval' => ['name' => 'Vendor Partner Verification & Approval', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.vendor_dispatch' => ['name' => 'Vendor Delivery Dispatch', 'module' => 'delivery', 'default_enabled' => true],
    'delivery.partner_progress' => ['name' => 'Partner Delivery Status Updates', 'module' => 'delivery', 'default_enabled' => true],
];
