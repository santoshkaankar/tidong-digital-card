<?php

namespace App\Console\Commands;

use App\Models\Authorization\Role;
use App\Models\Identity\AccountType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use RuntimeException;

class CreateInitialSuperAdmin extends Command
{
    protected $signature = 'app:create-initial-super-admin';

    protected $description = 'Create the first platform Super Admin securely';

    public function handle(): int
    {
        $superAdminRole = Role::where('code', 'super_admin')->first();
        $platformAdminType = AccountType::where('code', 'platform_admin')
            ->where('is_active', true)
            ->first();

        if (! $superAdminRole || ! $platformAdminType) {
            $this->error(
                'Super Admin role ya active platform_admin account type nahi mila.'
            );

            return self::FAILURE;
        }

        $existingAssignment = DB::table('user_role_assignments')
            ->where('role_id', $superAdminRole->id)
            ->exists();

        if ($existingAssignment) {
            $this->error(
                'Super Admin assignment pehle se maujood hai. Setup command band kar di gayi hai.'
            );

            return self::FAILURE;
        }

        $this->info('Initial Super Admin setup');
        $this->warn('Password sirf terminal mein enter karein, chat mein nahi.');

        $name = trim((string) $this->ask('Super Admin ka naam'));
        $email = Str::lower(trim((string) $this->ask('Email address')));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password enter karein (minimum 12 characters)');

        if (strlen($password) < 12) {
            $this->error('Password kam se kam 12 characters ka hona chahiye.');

            return self::FAILURE;
        }

        $confirmPassword = (string) $this->secret('Password dobara enter karein');

        if (! hash_equals($password, $confirmPassword)) {
            $this->error('Dono passwords match nahi karte.');

            return self::FAILURE;
        }

        try {
            $user = DB::transaction(function () use (
                $name,
                $email,
                $password,
                $superAdminRole,
                $platformAdminType
            ) {
                // Transaction ke andar dobara check, taaki duplicate setup na ho.
                $alreadyAssigned = DB::table('user_role_assignments')
                    ->where('role_id', $superAdminRole->id)
                    ->lockForUpdate()
                    ->exists();

                if ($alreadyAssigned) {
                    throw new RuntimeException(
                        'Super Admin assignment pehle se maujood hai.'
                    );
                }

                if (User::where('email', $email)->exists()) {
                    throw new RuntimeException(
                        'Yeh email pehle se registered hai.'
                    );
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                ]);

                $user->userAccountTypes()->create([
                    'account_type_id' => $platformAdminType->id,
                    'status' => 'active',
                    'activated_at' => now(),
                ]);

                $user->roleAssignments()->create([
                    'role_id' => $superAdminRole->id,
                    'scope_type' => 'platform',
                    'scope_id' => null,
                    'assigned_by' => null,
                    'starts_at' => now(),
                    'expires_at' => null,
                    'is_active' => true,
                ]);

                DB::table('audit_logs')->insert([
                    'actor_user_id' => null,
                    'action' => 'initial_super_admin_created',
                    'entity_type' => 'user',
                    'entity_id' => $user->id,
                    'reason' => 'Initial Super Admin setup',
                    'old_values' => null,
                    'new_values' => json_encode([
                        'role' => 'super_admin',
                        'account_type' => 'platform_admin',
                    ]),
                    'metadata' => json_encode([
                        'setup_command' => true,
                    ]),
                    'ip_address' => null,
                    'request_id' => null,
                    'created_at' => now(),
                ]);

                return $user;
            });
        } catch (\Throwable $exception) {
            report($exception);

            $this->error('Super Admin create nahi hua. Error: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Initial Super Admin successfully create ho gaya!');
        $this->line('User ID: '.$user->id);
        $this->line('Email: '.$user->email);
        $this->line('Role: Super Admin');
        $this->line('Account type: Platform Admin');

        return self::SUCCESS;
    }
}
