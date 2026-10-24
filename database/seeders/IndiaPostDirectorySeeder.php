<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class IndiaPostDirectorySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/india-post-directory.csv');
        if (!is_readable($path)) {
            throw new RuntimeException('India Post directory CSV is missing: '.$path);
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Cannot open India Post directory CSV.');
        }

        $count = 0;
        $batch = [];
        try {
            $header = fgetcsv($handle);
            if ($header === false) {
                throw new RuntimeException('India Post directory is empty.');
            }
            $header = array_map(fn ($h) => strtolower(trim((string) $h, " \t\n\r\0\x0B\xEF\xBB\xBF")), $header);
            foreach (['pincode', 'officename', 'district', 'statename'] as $required) {
                if (!in_array($required, $header, true)) {
                    throw new RuntimeException('Missing directory column: '.$required);
                }
            }

            while (($values = fgetcsv($handle)) !== false) {
                if (count($values) !== count($header)) {
                    continue;
                }
                $entry = array_combine($header, $values);
                $pin = trim((string) $entry['pincode']);
                $office = trim((string) $entry['officename']);
                $district = trim((string) $entry['district']);
                $state = trim((string) $entry['statename']);
                if (!preg_match('/^\d{6}$/D', $pin) || $office === '' || $district === '' || $state === '') {
                    continue;
                }
                // Pincode repeats across distinct post offices; never make pincode unique.
                $identity = array_map('mb_strtolower', [$pin, $office, $district, $state]);
                $coord = static function ($value, float $min, float $max): ?float {
                    $value = trim((string) $value);
                    return is_numeric($value) && (float) $value >= $min && (float) $value <= $max
                        ? round((float) $value, 7) : null;
                };
                $batch[] = [
                    'row_hash' => sha1(implode('|', $identity)),
                    'pincode' => $pin,
                    'post_office' => $office,
                    'district' => $district,
                    'state' => $state,
                    'circle' => trim((string) ($entry['circlename'] ?? '')) ?: null,
                    'region' => trim((string) ($entry['regionname'] ?? '')) ?: null,
                    'division' => trim((string) ($entry['divisionname'] ?? '')) ?: null,
                    'office_type' => trim((string) ($entry['officetype'] ?? '')) ?: null,
                    'delivery_type' => trim((string) ($entry['delivery'] ?? '')) ?: null,
                    'latitude' => $coord($entry['latitude'] ?? null, -90, 90),
                    'longitude' => $coord($entry['longitude'] ?? null, -180, 180),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (count($batch) >= 500) {
                    $count += DB::table('india_post_offices')->insertOrIgnore($batch);
                    $batch = [];
                }
            }
            if ($batch !== []) {
                $count += DB::table('india_post_offices')->insertOrIgnore($batch);
            }
        } finally {
            fclose($handle);
        }
        $this->command?->info("India Post directory import complete. New rows: {$count}.");
    }
}
