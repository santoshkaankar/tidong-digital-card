<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IndiaPostOfficeSchemaTest extends TestCase
{
    public function test_directory_and_branch_reference_columns_exist(): void
    {
        $this->assertTrue(Schema::hasTable('india_post_offices'));
        $this->assertTrue(Schema::hasColumns('india_post_offices',
            ['row_hash', 'pincode', 'post_office', 'district', 'state', 'latitude', 'longitude']));
        $this->assertTrue(Schema::hasColumns('branches',
            ['india_post_office_id', 'district', 'post_office']));
    }

    public function test_directory_row_can_be_stored_with_null_coordinates(): void
    {
        DB::beginTransaction();
        try {
            $hash = sha1('TEST|100001|SAMPLE');
            DB::table('india_post_offices')->insert([
                'row_hash' => $hash,
                'pincode' => '100001',
                'post_office' => 'Sample Office',
                'district' => 'SAMPLE DISTRICT',
                'state' => 'SAMPLE STATE',
                'latitude' => null,
                'longitude' => null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $this->assertDatabaseHas('india_post_offices', ['row_hash' => $hash]);
        } finally {
            DB::rollBack();
        }
    }
}
