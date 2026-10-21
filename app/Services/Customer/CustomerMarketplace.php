<?php

namespace App\Services\Customer;

use App\Models\Vendor\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Read-only customer directory. No customer PII, internal orders, or checkout.
 * All entry points share identical approval + active-branch predicates.
 */
class CustomerMarketplace
{
    /** @return array<string, array<string, mixed>> */
    public function categories(): array
    {
        $services = config('tidong_customer_services.services', []);
        // One constrained grouped query rather than one count query per tile.
        $counts = Business::query()
            ->select('business_type')
            ->selectRaw('COUNT(*) AS total')
            ->where('status', 'approved')
            ->where('is_accepting_orders', true)
            ->whereHas('vendor', fn (Builder $q) => $q->where('status', 'approved'))
            ->whereHas('branches', fn (Builder $q) => $q
                ->where('status', 'active')
                ->where('is_accepting_orders', true))
            ->groupBy('business_type')
            ->pluck('total', 'business_type');

        foreach ($services as $key => &$service) {
            $total = 0;
            if ($service['directory_enabled'] ?? false) {
                foreach ($service['types'] ?? [] as $type) {
                    $total += (int) ($counts[$type] ?? 0);
                }
            }
            $service['key'] = $key;
            $service['business_count'] = $total;
            $service['available'] = ($service['directory_enabled'] ?? false) && $total > 0;
        }
        unset($service);

        return $services;
    }

    public function validatedService(string $key): ?array
    {
        $service = config('tidong_customer_services.services.'.$key);
        if (! is_array($service) || ! ($service['directory_enabled'] ?? false)
            || empty($service['types'])) {
            return null;
        }
        return $service;
    }

    public function directory(string $key, ?string $term = null): LengthAwarePaginator
    {
        $service = $this->validatedService($key);
        abort_if($service === null, 404);

        $query = $this->availableBusinesses()->whereIn('business_type', $service['types']);
        if ($term !== null && $term !== '') {
            $query->where('name', 'like', '%'.$this->escapeLike($term).'%');
        }

        return $query->select(['id', 'name', 'slug', 'business_type'])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(9)
            ->withQueryString();
    }

    private function availableBusinesses(): Builder
    {
        return Business::query()
            ->where('status', 'approved')
            ->where('is_accepting_orders', true)
            ->whereHas('vendor', fn (Builder $q) => $q->where('status', 'approved'))
            ->whereHas('branches', fn (Builder $q) => $q
                ->where('status', 'active')
                ->where('is_accepting_orders', true));
    }

    private function escapeLike(string $value): string
    {
        // Backslash-escape wildcards for the MySQL LIKE query; input is still bound.
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
