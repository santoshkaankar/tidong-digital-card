<?php

/**
 * Navigation-only registry. Displaying a menu never grants permission.
 * Business-type and branch ownership must be enforced by every controller.
 * Entries with no implemented route are displayed as "Planned" (not links).
 */
return [
    'groups' => [
        'restaurant' => [
            'title' => 'Restaurant & Dining',
            'items' => [
                ['label' => 'Counter POS', 'key' => 'pos'],
                ['label' => 'Menu Library', 'key' => 'menu'],
                ['label' => 'Kitchen KDS', 'key' => 'kds'],
                ['label' => 'Table QR', 'key' => 'tables'],
                ['label' => 'QR Requests', 'key' => 'qr'],
                ['label' => 'Reservations', 'key' => null],
                ['label' => 'Restaurant Reports', 'key' => null],
            ],
        ],
        'sublimation' => [
            'title' => 'Sublimation Studio',
            'items' => [
                ['label' => 'Studio Overview', 'key' => 'studio'],
                ['label' => 'Add Product', 'key' => 'studio-add'],
                ['label' => 'Products & Pricing', 'key' => 'studio-prices'],
                ['label' => 'Custom Job Orders', 'key' => 'studio-orders'],
                ['label' => 'Design & Production', 'key' => 'studio-jobs'],
                ['label' => 'Materials & Stock', 'key' => null],
                ['label' => 'Studio Reports', 'key' => null],
            ],
        ],
        'retail' => [
            'title' => 'Retail & E-commerce',
            'items' => [
                ['label' => 'Products & Catalogue', 'key' => null],
                ['label' => 'Retail Billing', 'key' => null],
                ['label' => 'Offline / Online Prices', 'key' => null],
                ['label' => 'Inventory', 'key' => null],
                ['label' => 'Sales & Reports', 'key' => null],
            ],
        ],
        'distribution' => [
            'title' => 'Distribution & Trade',
            'items' => [
                ['label' => 'Retailer / Dealer Network', 'key' => null],
                ['label' => 'Wholesale Orders', 'key' => null],
                ['label' => 'Distributor / C&F Rates', 'key' => null],
                ['label' => 'Stock & Dispatch', 'key' => null],
                ['label' => 'Credit & Collection', 'key' => null],
                ['label' => 'Reports', 'key' => null],
            ],
        ],
        'production' => [
            'title' => 'Manufacturing & Processing',
            'items' => [
                ['label' => 'Raw Material', 'key' => null],
                ['label' => 'Production Batches', 'key' => null],
                ['label' => 'Processing & Yield', 'key' => null],
                ['label' => 'Finished Goods', 'key' => null],
                ['label' => 'Stock & Dispatch', 'key' => null],
                ['label' => 'Production Reports', 'key' => null],
            ],
        ],
        'transport' => [
            'title' => 'Transport & Logistics',
            'items' => [
                ['label' => 'Fleet & Vehicles', 'key' => null],
                ['label' => 'Bookings', 'key' => null],
                ['label' => 'Drivers & Staff', 'key' => null],
                ['label' => 'Trips & Dispatch', 'key' => null],
                ['label' => 'Transport Reports', 'key' => null],
            ],
        ],
        'events' => [
            'title' => 'Events & Marriage Planner',
            'items' => [
                ['label' => 'Packages & Services', 'key' => null],
                ['label' => 'Events & Bookings', 'key' => null],
                ['label' => 'Ritual Planner', 'key' => null],
                ['label' => 'Vendors & Staff', 'key' => null],
                ['label' => 'Reports', 'key' => null],
            ],
        ],
        'services' => [
            'title' => 'Service Business',
            'items' => [
                ['label' => 'Services', 'key' => null],
                ['label' => 'Appointments / Bookings', 'key' => null],
                ['label' => 'Staff & Assignments', 'key' => null],
                ['label' => 'Service Reports', 'key' => null],
            ],
        ],
    ],
    'type_groups' => [
        'restaurant' => 'restaurant', 'cafe' => 'restaurant', 'cloud_kitchen' => 'restaurant',
        'bakery' => 'restaurant', 'tiffin' => 'restaurant',
        'sublimation' => 'sublimation', 'custom_printing' => 'sublimation', 'printing' => 'sublimation',
        'retail' => 'retail', 'grocery' => 'retail', 'shopping' => 'retail', 'pharmacy' => 'retail',
        'wholesale' => 'distribution', 'wholesaler' => 'distribution', 'distributor' => 'distribution',
        'distribution' => 'distribution', 'cnf' => 'distribution', 'c_and_f' => 'distribution',
        'manufacturing' => 'production', 'factory' => 'production', 'processing' => 'production', 'processing_unit' => 'production',
        'taxi' => 'transport', 'car_rental' => 'transport', 'logistics' => 'transport', 'delivery' => 'transport',
        'travel_agency' => 'transport',
        'marriage_planner' => 'events', 'event_management' => 'events',
        'salon' => 'services', 'spa' => 'services', 'hotel' => 'services', 'money_exchange' => 'services',
        'real_estate' => 'services', 'education' => 'services', 'repair_services' => 'services',
        'professional_services' => 'services', 'other' => 'services',
    ],
];
