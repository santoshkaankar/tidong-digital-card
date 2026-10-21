<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('india_post_offices', function (Blueprint $table): void {
            $table->id();
            $table->char('row_hash', 40)->unique();
            $table->string('pincode', 6)->index();
            $table->string('post_office', 180);
            $table->string('district', 150);
            $table->string('state', 100);
            $table->string('circle', 120)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('division', 120)->nullable();
            $table->string('office_type', 20)->nullable();
            $table->string('delivery_type', 30)->nullable();
            // Approximate source-directory coordinates ONLY; not a building/delivery pin.
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
            $table->index(['state', 'district'], 'india_post_state_district_idx');
            $table->index(['pincode', 'post_office'], 'india_post_pin_office_idx');
        });

        Schema::table('branches', function (Blueprint $table): void {
            $table->foreignId('india_post_office_id')
                ->nullable()->constrained('india_post_offices')->nullOnDelete();
            $table->string('district', 150)->nullable();
            $table->string('post_office', 180)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('india_post_office_id');
            $table->dropColumn(['district', 'post_office']);
        });
        Schema::dropIfExists('india_post_offices');
    }
};
