<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pincode; // Agar aapka model Pincode hai, ya direct DB use kar sakte hain

class PincodeSeeder extends Seeder
{
    public function run()
    {
        $path = public_path('all_india_pincode_directory_2025.csv');
        
        if (!file_exists($path)) {
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Skip header

        $batchSize = 1000;
        $batch = [];

        // Purana data clear karein agar dobara run karein
        DB::table('pincodes')->truncate();

        while (($row = fgetcsv($file)) !== false) {
            // CSV columns mapping based on your file:
            // 0: circlename, 1: regionname, 2: divisionname, 3: officename, 4: pincode, 
            // 5: officetype, 6: delivery, 7: district, 8: statename, 9: latitude, 10: longitude
            
            $lat = is_numeric($row[9]) ? (float)$row[9] : null;
            $long = is_numeric($row[10]) ? (float)$row[10] : null;

            // Double check swap safety
            if ($lat !== null && $long !== null && $lat >= 65 && $lat <= 98 && $long >= 6 && $long <= 40) {
                $temp = $lat;
                $lat = $long;
                $long = $temp;
            }

            $batch[] = [
                'circlename'   => $row[0] ?? null,
                'regionname'   => $row[1] ?? null,
                'divisionname' => $row[2] ?? null,
                'officename'   => $row[3] ?? null,
                'pincode'      => $row[4] ?? null,
                'officetype'   => $row[5] ?? null,
                'delivery'     => $row[6] ?? null,
                'district'     => $row[7] ?? null,
                'statename'    => $row[8] ?? null,
                'latitude'     => $lat,
                'longitude'    => $long,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            if (count($batch) >= $batchSize) {
                DB::table('pincodes')->insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table('pincodes')->insert($batch);
        }

        fclose($file);
    }
}