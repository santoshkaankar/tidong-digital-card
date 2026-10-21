<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/** Read-only: shows valid local targets before running the existing sample seeder. */
class ListSublimationBranches extends Command
{
    protected $signature = 'studio:list-branches';

    protected $description = 'List approved sublimation businesses and their active branches (read-only).';

    public function handle(): int
    {
        $rows = DB::table('businesses as b')
            ->join('vendors as v', 'v.id', '=', 'b.vendor_id')
            ->join('branches as br', 'br.business_id', '=', 'b.id')
            ->whereIn('b.business_type', ['sublimation', 'custom_printing', 'printing'])
            ->where('b.status', 'approved')->where('v.status', 'approved')
            ->where('br.status', 'active')
            ->whereNull('b.deleted_at')->whereNull('v.deleted_at')
            ->whereNull('br.deleted_at')
            ->orderBy('b.id')->orderBy('br.id')
            ->get(['b.id as business_id', 'b.name as business_name',
                'br.id as branch_id', 'br.name as branch_name']);

        if ($rows->isEmpty()) {
            $this->warn('No approved Sublimation business with active branch was found. No data was changed.');
            return self::SUCCESS;
        }

        $this->table(['Business ID', 'Business', 'Branch ID', 'Branch'], $rows->map(fn ($r) => [
            $r->business_id, $r->business_name, $r->branch_id, $r->branch_name,
        ])->all());
        $this->line('After selecting verified IDs: php artisan studio:seed-samples BUSINESS_ID BRANCH_ID --dry-run');
        return self::SUCCESS;
    }
}
