<?php

namespace App\Services\Platform;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

/** Technical global availability only; authorization remains in each domain service. */
class FeatureRegistry
{
    public function isEnabled(string $code): bool
    {
        $feature = (config('tidong_features', [])[$code] ?? null);
        if (! is_array($feature) || ! app(ModuleRegistry::class)->isEnabled($feature['module'])) {
            return false;
        }
        if (! Schema::hasTable('platform_features')) {
            return (bool) ($feature['default_enabled'] ?? false);
        }
        $row = DB::table('platform_features')->where('code', $code)->first();
        return $row ? (bool) $row->is_enabled : (bool) ($feature['default_enabled'] ?? false);
    }

    public function overview(): array
    {
        $rows = Schema::hasTable('platform_features')
            ? DB::table('platform_features')->get()->keyBy('code') : collect();
        $features = [];
        foreach (config('tidong_features', []) as $code => $definition) {
            $features[] = [
                'code' => $code, 'name' => $definition['name'], 'module' => $definition['module'],
                'enabled' => $this->isEnabled($code),
                'module_enabled' => app(ModuleRegistry::class)->isEnabled($definition['module']),
                'overridden' => $rows->has($code),
            ];
        }
        return $features;
    }

    public function setEnabled(string $code, bool $enabled, int $actorId): void
    {
        $feature = (config('tidong_features', [])[$code] ?? null);
        if (! is_array($feature)) {
            throw ValidationException::withMessages(['feature' => 'Unreviewed feature cannot be controlled.']);
        }
        DB::transaction(function () use ($code, $enabled, $actorId, $feature): void {
            $old = DB::table('platform_features')->where('code', $code)->lockForUpdate()->first();
            $was = $old ? (bool) $old->is_enabled : (bool) ($feature['default_enabled'] ?? false);
            DB::table('platform_features')->updateOrInsert(['code' => $code], [
                'name' => $feature['name'], 'module_code' => $feature['module'],
                'is_enabled' => $enabled, 'updated_by' => $actorId,
                'created_at' => $old->created_at ?? now(), 'updated_at' => now(),
            ]);
            DB::table('audit_logs')->insert([
                'actor_user_id' => $actorId, 'action' => 'feature.availability.changed',
                'entity_type' => 'platform_feature', 'metadata' => json_encode(['code' => $code]),
                'old_values' => json_encode(['enabled' => $was]),
                'new_values' => json_encode(['enabled' => $enabled]), 'created_at' => now(),
            ]);
        });
    }
}
