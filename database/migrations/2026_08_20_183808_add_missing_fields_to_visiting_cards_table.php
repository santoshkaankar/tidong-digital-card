<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            // Missing Data Fields
            if (!Schema::hasColumn('visiting_cards', 'other_details')) {
                $table->text('other_details')->nullable()->after('services_or_products');
            }
            if (!Schema::hasColumn('visiting_cards', 'location_url')) {
                $table->text('location_url')->nullable()->after('map_location_link');
            }
            if (!Schema::hasColumn('visiting_cards', 'website')) {
                $table->string('website')->nullable()->after('website_link');
            }

            // Missing Display Toggles
            if (!Schema::hasColumn('visiting_cards', 'show_area')) {
                $table->boolean('show_area')->default(true)->after('show_address');
            }
            if (!Schema::hasColumn('visiting_cards', 'show_pincode')) {
                $table->boolean('show_pincode')->default(true)->after('show_area');
            }
            if (!Schema::hasColumn('visiting_cards', 'show_city')) {
                $table->boolean('show_city')->default(true)->after('show_pincode');
            }
            if (!Schema::hasColumn('visiting_cards', 'show_state')) {
                $table->boolean('show_state')->default(true)->after('show_city');
            }
            if (!Schema::hasColumn('visiting_cards', 'show_other_details')) {
                $table->boolean('show_other_details')->default(true)->after('show_state');
            }
            if (!Schema::hasColumn('visiting_cards', 'show_location_url')) {
                $table->boolean('show_location_url')->default(true)->after('show_other_details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visiting_cards', function (Blueprint $table) {
            $table->dropColumn([
                'other_details',
                'location_url',
                'website',
                'show_area',
                'show_pincode',
                'show_city',
                'show_state',
                'show_other_details',
                'show_location_url',
            ]);
        });
    }
};