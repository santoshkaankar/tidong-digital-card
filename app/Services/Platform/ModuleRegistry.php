<?php

namespace App\Services\Platform;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ModuleRegistry
{
    public function catalog(): array
    {
        return (array) config('tidong_modules', []);
    }

    public function installed(string $code): bool
    {
        return (bool) ($this->catalog()[$code]['installed'] ?? false);
    }

    public function isEnabled(string $code): bool
    {
        $definition = $this->catalog()[$code] ?? null;
        if (! $definition || ! ($definition['installed'] ?? false)) {
            return false;
        }

        // Existing modules retain their previous availability until explicitly managed.
        $fallback = (bool) ($definition['default_enabled'] ?? false);
        if (! Schema::hasTable('platform_modules')) {
            return $fallback;
        }

        $row = DB::table('platform_modules')->where('code', $code)->first();
        return $row ? (bool) $row->is_enabled : $fallback;
    }

    /** Vendor overrides are opt-out: no row means the service is available. */
    public function isEnabledForVendor(string $code, int $vendorId): bool
    {
        if (! $this->installed($code)) {
            return false;
        }
        if (! Schema::hasTable('vendor_module_overrides')) {
            return true;
        }
        $override = DB::table('vendor_module_overrides')
            ->where('vendor_id', $vendorId)->where('module_code', $code)->first();
        return $override ? (bool) $override->is_enabled : true;
    }

    public function setVendorEnabled(string $code, int $vendorId, bool $enabled, int $actorId): void
    {
        if (! $this->installed($code)) {
            throw ValidationException::withMessages(['module' => 'Module is not installed.']);
        }
        if (! Schema::hasTable('vendor_module_overrides')) {
            throw ValidationException::withMessages(['module' => 'Vendor controls are not installed.']);
        }
        DB::transaction(function () use ($code, $vendorId, $enabled, $actorId): void {
            if (! DB::table('vendors')->where('id', $vendorId)->whereNull('deleted_at')->exists()) {
                throw ValidationException::withMessages(['vendor' => 'Vendor not found.']);
            }
            DB::table('vendor_module_overrides')->updateOrInsert(
                ['vendor_id' => $vendorId, 'module_code' => $code],
                ['is_enabled' => $enabled, 'updated_by' => $actorId, 'updated_at' => now()]
            );
        });
    }

    public function overview(): array
    {
        $records = Schema::hasTable('platform_modules')
            ? DB::table('platform_modules')->get()->keyBy('code') : collect();
        $result = [];
        foreach ($this->catalog() as $code => $definition) {
            $row = $records->get($code);
            $result[] = [
                'code' => $code,
                'name' => $definition['name'],
                'installed' => (bool) ($definition['installed'] ?? false),
                'registered' => (bool) $row,
                'enabled' => $row ? (bool) $row->is_enabled : $this->isEnabled($code),
            ];
        }
        return $result;
    }

    public function register(string $code, int $actorId): void
    {
        $definition = $this->catalog()[$code] ?? null;
        if (! $definition || ! ($definition['installed'] ?? false)) {
            throw ValidationException::withMessages(['module' => 'This module has not been deployed or reviewed.']);
        }
        DB::transaction(function () use ($code, $definition, $actorId): void {
            if (DB::table('platform_modules')->where('code', $code)->exists()) {
                throw ValidationException::withMessages(['module' => 'This module is already registered.']);
            }
            DB::table('platform_modules')->insert([
                'code' => $code, 'name' => $definition['name'],
                'is_enabled' => (bool) ($definition['default_enabled'] ?? false),
                'updated_by' => $actorId, 'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->audit($actorId, 'module.registered', $code, null, (bool) ($definition['default_enabled'] ?? false));
        });
    }

    public function setEnabled(string $code, bool $enabled, int $actorId): void
    {
        if (! $this->installed($code)) {
            throw ValidationException::withMessages(['module' => 'Module is not installed.']);
        }
        DB::transaction(function () use ($code, $enabled, $actorId): void {
            $row = DB::table('platform_modules')->where('code', $code)->lockForUpdate()->first();
            if (! $row) {
                throw ValidationException::withMessages(['module' => 'Register the module first.']);
            }
            if ((bool) $row->is_enabled === $enabled) {
                return;
            }
            DB::table('platform_modules')->where('code', $code)->update([
                'is_enabled' => $enabled, 'updated_by' => $actorId, 'updated_at' => now(),
            ]);
            $this->audit($actorId, $enabled ? 'module.enabled' : 'module.disabled',
                $code, (bool) $row->is_enabled, $enabled);
        });
    }

    private function audit(int $actorId, string $action, string $code, ?bool $before, bool $after): void
    {
        DB::table('audit_logs')->insert([
            'actor_user_id' => $actorId, 'action' => $action,
            'entity_type' => 'platform_module', 'entity_id' => null,
            'metadata' => json_encode(['code' => $code]),
            'old_values' => json_encode(['enabled' => $before]),
            'new_values' => json_encode(['enabled' => $after]),
            'created_at' => now(),
        ]);
    }
}
