<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PincodeSeeder extends Seeder
{
    public function run(): void
    {
        // Increase limits for processing large 1.6 lakh+ rows dataset
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $path = storage_path('app/all_india_pincode_directory_2025.csv');

        if (!file_exists($path) || !is_readable($path)) {
            if ($this->command) {
                $this->command->error("CSV file not found at: {$path}");
            }
            return;
        }

        $header = null;
        $rows = [];
        $batchSize = 1000;
        $rowCount = 0;
        
        if (($handle = fopen($path, 'r')) !== false) {
            if ($this->command) {
                $this->command->info('Reading CSV and seeding data...');
            }
            
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (!$header) {
                    $header = $data;
                } else {
                    if (count($header) == count($data)) {
                        $row = array_combine($header, $data);

                        $lat = $row['latitude'] ?? null;
                        $lon = $row['longitude'] ?? null;

                        $cleanLat = null;
                        $cleanLon = null;

                        if (!empty($lat) && preg_match('/-?\d+(\.\d+)?/', $lat, $matchLat)) {
                            $val = (float)$matchLat[0];
                            if ($val >= -90 && $val <= 90) {
                                $cleanLat = round($val, 6);
                            }
                        }

                        if (!empty($lon) && preg_match('/-?\d+(\.\d+)?/', $lon, $matchLon)) {
                            $val = (float)$matchLon[0];
                            if ($val >= -180 && $val <= 180) {
                                $cleanLon = round($val, 6);
                            }
                        }

                        $rows[] = [
                            'circle_name'     => $row['circlename'] ?? null,
                            'region_name'     => $row['regionname'] ?? null,
                            'division_name'   => $row['divisionname'] ?? null,
                            'office_name'     => $row['officename'] ?? null,
                            'pincode'         => $row['pincode'] ?? null,
                            'office_type'     => $row['officetype'] ?? null,
                            'delivery_status' => $row['delivery'] ?? null,
                            'district'        => $row['district'] ?? null,
                            'state_name'      => $row['statename'] ?? null,
                            'latitude'        => $cleanLat,
                            'longitude'       => $cleanLon,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];

                        $rowCount++;

                        if (count($rows) === $batchSize) {
                            DB::table('pincodes')->insert($rows);
                            $rows = [];
                        }
                    }
                }
            }
            fclose($handle);

            if (!empty($rows)) {
                DB::table('pincodes')->insert($rows);
            }

            if ($this->command) {
                $this->command->info("Total Processed & Inserted Rows: {$rowCount}");
                $this->command->info('All 1.6 Lakh+ Pincodes seeded successfully!');
            }
        } else {
            if ($this->command) {
                $this->command->error('Could not open CSV file.');
            }
        }
    }
}