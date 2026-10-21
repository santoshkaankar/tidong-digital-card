<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DigitalFoundationSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. ACCOUNT TYPES
        |--------------------------------------------------------------------------
        */

        $accountTypes = [
            [
                'code' => 'customer',
                'name' => 'Customer',
                'is_active' => true,
            ],
            [
                'code' => 'vendor',
                'name' => 'Vendor',
                'is_active' => true,
            ],
            [
                'code' => 'delivery',
                'name' => 'Delivery',
                'is_active' => true,
            ],
            [
                'code' => 'admin',
                'name' => 'Admin',
                'is_active' => true,
            ],
        ];

        foreach ($accountTypes as $accountType) {
            DB::table('account_types')->updateOrInsert(
                ['code' => $accountType['code']],
                [
                    'name' => $accountType['name'],
                    'is_active' => $accountType['is_active'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. ROLES
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'code' => 'super_admin',
                'name' => 'Super Admin',
                'description' => 'Full platform control.',
                'is_system' => true,
            ],
            [
                'code' => 'admin',
                'name' => 'Admin',
                'description' => 'Platform administration role.',
                'is_system' => true,
            ],
            [
                'code' => 'approval_manager',
                'name' => 'Approval Manager',
                'description' => 'Manages vendor and business approval operations.',
                'is_system' => true,
            ],
            [
                'code' => 'approval_employee',
                'name' => 'Approval Employee',
                'description' => 'Handles assigned approval and rejection work.',
                'is_system' => true,
            ],
            [
                'code' => 'delivery_manager',
                'name' => 'Delivery Manager',
                'description' => 'Manages delivery operations.',
                'is_system' => true,
            ],
            [
                'code' => 'support_manager',
                'name' => 'Support Manager',
                'description' => 'Manages customer and vendor support operations.',
                'is_system' => true,
            ],
            [
                'code' => 'support_employee',
                'name' => 'Support Employee',
                'description' => 'Handles customer and vendor support work.',
                'is_system' => true,
            ],
            [
                'code' => 'accounts_manager',
                'name' => 'Accounts Manager',
                'description' => 'Manages financial and accounting operations.',
                'is_system' => true,
            ],
            [
                'code' => 'accounts_employee',
                'name' => 'Accounts Employee',
                'description' => 'Handles assigned accounting operations.',
                'is_system' => true,
            ],
            [
                'code' => 'operations_manager',
                'name' => 'Operations Manager',
                'description' => 'Manages platform operations.',
                'is_system' => true,
            ],
            [
                'code' => 'operations_employee',
                'name' => 'Operations Employee',
                'description' => 'Handles assigned operational work.',
                'is_system' => true,
            ],
            [
                'code' => 'vendor_owner',
                'name' => 'Vendor Owner',
                'description' => 'Owner of one or more vendor businesses.',
                'is_system' => true,
            ],
            [
                'code' => 'vendor_manager',
                'name' => 'Vendor Manager',
                'description' => 'Manages vendor business operations.',
                'is_system' => true,
            ],
            [
                'code' => 'vendor_employee',
                'name' => 'Vendor Employee',
                'description' => 'Works for vendor businesses and branches.',
                'is_system' => true,
            ],
            [
                'code' => 'delivery_partner',
                'name' => 'Delivery Partner',
                'description' => 'Handles delivery assignments.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['code' => $role['code']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'is_system' => $role['is_system'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            [
                'code' => 'dashboard.view',
                'name' => 'View Dashboard',
                'module' => 'dashboard',
                'description' => 'View dashboard.',
            ],

            // Users
            [
                'code' => 'users.view',
                'name' => 'View Users',
                'module' => 'users',
                'description' => 'View users.',
            ],
            [
                'code' => 'users.create',
                'name' => 'Create Users',
                'module' => 'users',
                'description' => 'Create users.',
            ],
            [
                'code' => 'users.update',
                'name' => 'Update Users',
                'module' => 'users',
                'description' => 'Update users.',
            ],
            [
                'code' => 'users.suspend',
                'name' => 'Suspend Users',
                'module' => 'users',
                'description' => 'Suspend users.',
            ],

            // Vendors
            [
                'code' => 'vendors.view',
                'name' => 'View Vendors',
                'module' => 'vendors',
                'description' => 'View vendors.',
            ],
            [
                'code' => 'vendors.create',
                'name' => 'Create Vendors',
                'module' => 'vendors',
                'description' => 'Create vendors.',
            ],
            [
                'code' => 'vendors.update',
                'name' => 'Update Vendors',
                'module' => 'vendors',
                'description' => 'Update vendors.',
            ],
            [
                'code' => 'vendors.approve',
                'name' => 'Approve Vendors',
                'module' => 'vendors',
                'description' => 'Approve vendor applications.',
            ],
            [
                'code' => 'vendors.reject',
                'name' => 'Reject Vendors',
                'module' => 'vendors',
                'description' => 'Reject vendor applications with reason.',
            ],
            [
                'code' => 'vendors.suspend',
                'name' => 'Suspend Vendors',
                'module' => 'vendors',
                'description' => 'Suspend vendors.',
            ],

            // Businesses
            [
                'code' => 'businesses.view',
                'name' => 'View Businesses',
                'module' => 'businesses',
                'description' => 'View businesses.',
            ],
            [
                'code' => 'businesses.create',
                'name' => 'Create Businesses',
                'module' => 'businesses',
                'description' => 'Create businesses.',
            ],
            [
                'code' => 'businesses.update',
                'name' => 'Update Businesses',
                'module' => 'businesses',
                'description' => 'Update businesses.',
            ],
            [
                'code' => 'businesses.approve',
                'name' => 'Approve Businesses',
                'module' => 'businesses',
                'description' => 'Approve businesses.',
            ],
            [
                'code' => 'businesses.reject',
                'name' => 'Reject Businesses',
                'module' => 'businesses',
                'description' => 'Reject businesses with reason.',
            ],
            [
                'code' => 'businesses.ban',
                'name' => 'Ban Businesses',
                'module' => 'businesses',
                'description' => 'Ban businesses.',
            ],

            // Branches
            [
                'code' => 'branches.view',
                'name' => 'View Branches',
                'module' => 'branches',
                'description' => 'View business branches.',
            ],
            [
                'code' => 'branches.create',
                'name' => 'Create Branches',
                'module' => 'branches',
                'description' => 'Create branches.',
            ],
            [
                'code' => 'branches.update',
                'name' => 'Update Branches',
                'module' => 'branches',
                'description' => 'Update branches.',
            ],

            // Employees
            [
                'code' => 'employees.view',
                'name' => 'View Employees',
                'module' => 'employees',
                'description' => 'View employees.',
            ],
            [
                'code' => 'employees.create',
                'name' => 'Create Employees',
                'module' => 'employees',
                'description' => 'Create employees.',
            ],
            [
                'code' => 'employees.update',
                'name' => 'Update Employees',
                'module' => 'employees',
                'description' => 'Update employees.',
            ],
            [
                'code' => 'employees.assign',
                'name' => 'Assign Employees',
                'module' => 'employees',
                'description' => 'Assign employees to businesses or branches.',
            ],
            [
                'code' => 'employees.transfer',
                'name' => 'Transfer Employees',
                'module' => 'employees',
                'description' => 'Transfer employees between assignments.',
            ],

            // Approvals
            [
                'code' => 'approvals.view',
                'name' => 'View Approvals',
                'module' => 'approvals',
                'description' => 'View approval requests.',
            ],
            [
                'code' => 'approvals.approve',
                'name' => 'Approve Requests',
                'module' => 'approvals',
                'description' => 'Approve assigned requests.',
            ],
            [
                'code' => 'approvals.reject',
                'name' => 'Reject Requests',
                'module' => 'approvals',
                'description' => 'Reject assigned requests with reason.',
            ],

            // Audit
            [
                'code' => 'audit.view',
                'name' => 'View Audit Logs',
                'module' => 'audit',
                'description' => 'View system audit logs.',
            ],

            // Finance
            [
                'code' => 'finance.view',
                'name' => 'View Finance',
                'module' => 'finance',
                'description' => 'View financial records.',
            ],
            [
                'code' => 'finance.manage',
                'name' => 'Manage Finance',
                'module' => 'finance',
                'description' => 'Manage authorized financial operations.',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['code' => $permission['code']],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. ROLE → PERMISSION MAPPING
        |--------------------------------------------------------------------------
        */

        $superAdminRole = DB::table('roles')
            ->where('code', 'super_admin')
            ->first();

        $adminRole = DB::table('roles')
            ->where('code', 'admin')
            ->first();

        $approvalManagerRole = DB::table('roles')
            ->where('code', 'approval_manager')
            ->first();

        $approvalEmployeeRole = DB::table('roles')
            ->where('code', 'approval_employee')
            ->first();

        $deliveryManagerRole = DB::table('roles')
            ->where('code', 'delivery_manager')
            ->first();

        $supportManagerRole = DB::table('roles')
            ->where('code', 'support_manager')
            ->first();

        $supportEmployeeRole = DB::table('roles')
            ->where('code', 'support_employee')
            ->first();

        $accountsManagerRole = DB::table('roles')
            ->where('code', 'accounts_manager')
            ->first();

        $accountsEmployeeRole = DB::table('roles')
            ->where('code', 'accounts_employee')
            ->first();

        $operationsManagerRole = DB::table('roles')
            ->where('code', 'operations_manager')
            ->first();

        $operationsEmployeeRole = DB::table('roles')
            ->where('code', 'operations_employee')
            ->first();

        $vendorOwnerRole = DB::table('roles')
            ->where('code', 'vendor_owner')
            ->first();

        $vendorManagerRole = DB::table('roles')
            ->where('code', 'vendor_manager')
            ->first();

        $vendorEmployeeRole = DB::table('roles')
            ->where('code', 'vendor_employee')
            ->first();

        $deliveryPartnerRole = DB::table('roles')
            ->where('code', 'delivery_partner')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Helper: permission IDs by code
        |--------------------------------------------------------------------------
        */

        $permissionIds = DB::table('permissions')
            ->pluck('id', 'code');

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($superAdminRole) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id' => $superAdminRole->id,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        if ($adminRole) {
            $adminPermissions = [
                'dashboard.view',
                'users.view',
                'users.create',
                'users.update',
                'users.suspend',
                'vendors.view',
                'vendors.update',
                'vendors.approve',
                'vendors.reject',
                'businesses.view',
                'businesses.update',
                'businesses.approve',
                'businesses.reject',
                'branches.view',
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.assign',
                'employees.transfer',
                'approvals.view',
                'approvals.approve',
                'approvals.reject',
                'audit.view',
                'finance.view',
            ];

            foreach ($adminPermissions as $code) {
                if (isset($permissionIds[$code])) {
                    DB::table('role_permissions')->updateOrInsert(
                        [
                            'role_id' => $adminRole->id,
                            'permission_id' => $permissionIds[$code],
                        ],
                        [
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Manager
        |--------------------------------------------------------------------------
        */

        if ($approvalManagerRole) {
            $permissions = [
                'dashboard.view',
                'vendors.view',
                'vendors.approve',
                'vendors.reject',
                'businesses.view',
                'businesses.approve',
                'businesses.reject',
                'approvals.view',
                'approvals.approve',
                'approvals.reject',
                'audit.view',
            ];

            $this->assignPermissions(
                $approvalManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Employee
        |--------------------------------------------------------------------------
        */

        if ($approvalEmployeeRole) {
            $permissions = [
                'dashboard.view',
                'vendors.view',
                'vendors.approve',
                'vendors.reject',
                'businesses.view',
                'businesses.approve',
                'businesses.reject',
                'approvals.view',
                'approvals.approve',
                'approvals.reject',
            ];

            $this->assignPermissions(
                $approvalEmployeeRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delivery Manager
        |--------------------------------------------------------------------------
        */

        if ($deliveryManagerRole) {
            $permissions = [
                'dashboard.view',
                'users.view',
                'audit.view',
            ];

            $this->assignPermissions(
                $deliveryManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Support Manager
        |--------------------------------------------------------------------------
        */

        if ($supportManagerRole) {
            $permissions = [
                'dashboard.view',
                'users.view',
                'users.update',
                'vendors.view',
                'businesses.view',
                'audit.view',
            ];

            $this->assignPermissions(
                $supportManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Support Employee
        |--------------------------------------------------------------------------
        */

        if ($supportEmployeeRole) {
            $permissions = [
                'dashboard.view',
                'users.view',
                'vendors.view',
                'businesses.view',
            ];

            $this->assignPermissions(
                $supportEmployeeRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Accounts Manager
        |--------------------------------------------------------------------------
        */

        if ($accountsManagerRole) {
            $permissions = [
                'dashboard.view',
                'finance.view',
                'finance.manage',
                'audit.view',
            ];

            $this->assignPermissions(
                $accountsManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Accounts Employee
        |--------------------------------------------------------------------------
        */

        if ($accountsEmployeeRole) {
            $permissions = [
                'dashboard.view',
                'finance.view',
            ];

            $this->assignPermissions(
                $accountsEmployeeRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Operations Manager
        |--------------------------------------------------------------------------
        */

        if ($operationsManagerRole) {
            $permissions = [
                'dashboard.view',
                'users.view',
                'vendors.view',
                'businesses.view',
                'branches.view',
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.assign',
                'employees.transfer',
                'audit.view',
            ];

            $this->assignPermissions(
                $operationsManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Operations Employee
        |--------------------------------------------------------------------------
        */

        if ($operationsEmployeeRole) {
            $permissions = [
                'dashboard.view',
                'vendors.view',
                'businesses.view',
                'branches.view',
                'employees.view',
            ];

            $this->assignPermissions(
                $operationsEmployeeRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vendor Owner
        |--------------------------------------------------------------------------
        */

        if ($vendorOwnerRole) {
            $permissions = [
                'dashboard.view',
                'businesses.view',
                'businesses.create',
                'businesses.update',
                'branches.view',
                'branches.create',
                'branches.update',
                'employees.view',
                'employees.create',
                'employees.update',
                'employees.assign',
            ];

            $this->assignPermissions(
                $vendorOwnerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vendor Manager
        |--------------------------------------------------------------------------
        */

        if ($vendorManagerRole) {
            $permissions = [
                'dashboard.view',
                'businesses.view',
                'businesses.update',
                'branches.view',
                'branches.update',
                'employees.view',
                'employees.assign',
            ];

            $this->assignPermissions(
                $vendorManagerRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Vendor Employee
        |--------------------------------------------------------------------------
        */

        if ($vendorEmployeeRole) {
            $permissions = [
                'dashboard.view',
                'businesses.view',
                'branches.view',
            ];

            $this->assignPermissions(
                $vendorEmployeeRole->id,
                $permissions,
                $permissionIds
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Delivery Partner
        |--------------------------------------------------------------------------
        */

        if ($deliveryPartnerRole) {
            $permissions = [
                'dashboard.view',
            ];

            $this->assignPermissions(
                $deliveryPartnerRole->id,
                $permissions,
                $permissionIds
            );
        }
    }

    /**
     * Assign permissions to a role.
     */
    private function assignPermissions(
        int $roleId,
        array $permissionCodes,
        $permissionIds
    ): void {
        foreach ($permissionCodes as $code) {
            if (!isset($permissionIds[$code])) {
                continue;
            }

            DB::table('role_permissions')->updateOrInsert(
                [
                    'role_id' => $roleId,
                    'permission_id' => $permissionIds[$code],
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}