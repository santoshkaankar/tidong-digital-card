<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IndiaPostOfficeDirectorySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/india-post-directory.csv');
        if (! is_readable($path)) {
            throw new RuntimeException('India Post CSV missing: '.$path);
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Unable to open India Post CSV.');
        }

        $required = ['circlename', 'regionname', 'divisionname', 'officename', 'pincode', 'officetype', 'delivery', 'district', 'statename', 'latitude', 'longitude'];
        $first = fgetcsv($handle);
        $heading = array_map(fn ($v) => strtolower(trim((string) $v, " \t\n\r\0\x0B\xEF\xBB\xBF")), $first ?: []);
        if (array_diff($required, $heading)) {
            fclose($handle);
            throw new RuntimeException('Unexpected CSV headers; import stopped.');
        }

        $batch = [];
        $read = 0;
        $skipped = 0;
        $stamp = now()->toDateTimeString();
        $updates = ['pincode', 'post_office', 'district', 'state', 'circle', 'region', 'division', 'office_type', 'delivery_type', 'latitude', 'longitude', 'updated_at'];

        try {
            while (($values = fgetcsv($handle)) !== false) {
                $read++;
                if (count($values) !== count($heading)) { $skipped++; continue; }
                $row = array_combine($heading, array_map(static fn ($v) => trim((string) $v), $values));
                $pin = $row['pincode'];
                $state = strtoupper($row['statename']);
                $district = strtoupper($row['district']);
                $office = $row['officename'];
                if (! preg_match('/^[1-9][0-9]{5}$/D', $pin)
                    || in_array($state, ['', 'NA', 'N/A', 'NULL'], true)
                    || in_array($district, ['', 'NA', 'N/A', 'NULL'], true)
                    || $office === '' || strtoupper($office) === 'NA') {
                    $skipped++;
                    continue;
                }

                $lat = is_numeric($row['latitude']) ? (float) $row['latitude'] : null;
                $lon = is_numeric($row['longitude']) ? (float) $row['longitude'] : null;
                // Out-of-bounds data is not trusted. Still NEVER use these as exact address pins.
                if ($lat === null || $lon === null || $lat < 6 || $lat > 38 || $lon < 68 || $lon > 98) {
                    $lat = null;
                    $lon = null;
                }
                $identity = implode('|', [$pin, $state, $district, mb_strtolower($office),
                    strtoupper($row['divisionname']), strtoupper($row['officetype'])]);
                $batch[] = [
                    'row_hash' => sha1($identity),
                    'pincode' => $pin,
                    'post_office' => mb_substr($office, 0, 180),
                    'district' => mb_substr($district, 0, 150),
                    'state' => mb_substr($state, 0, 100),
                    'circle' => mb_substr($row['circlename'], 0, 120),
                    'region' => mb_substr($row['regionname'], 0, 120),
                    'division' => mb_substr($row['divisionname'], 0, 120),
                    'office_type' => mb_substr($row['officetype'], 0, 20),
                    'delivery_type' => mb_substr($row['delivery'], 0, 30),
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'created_at' => $stamp,
                    'updated_at' => $stamp,
                ];
                if (count($batch) >= 300) {
                    DB::table('india_post_offices')->upsert($batch, ['row_hash'], $updates);
                    $batch = [];
                }
            }
            if ($batch) {
                DB::table('india_post_offices')->upsert($batch, ['row_hash'], $updates);
            }
        } finally {
            fclose($handle);
        }
        $this->command?->info("India Post rows read: {$read}; skipped for invalid core address: {$skipped}.");
        $this->command?->info('Unique postal directory entries: '.DB::table('india_post_offices')->count());
    }
}
