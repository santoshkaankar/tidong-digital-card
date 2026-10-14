<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('referral_id', 12)->nullable()->unique();
                $table->string('username')->nullable()->unique();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->tinyInteger('email_verified')->default(0);
                $table->tinyInteger('mobile_verified')->default(1);
                $table->string('mobile')->nullable()->unique();
                $table->string('password');
                $table->string('role')->default('member');
                $table->string('status')->default('pending');
                
                // MLM & Affiliate Fields
                $table->unsignedBigInteger('sponsor_id')->nullable();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->enum('position', ['left', 'right'])->nullable();
                $table->integer('left_count')->default(0);
                $table->integer('right_count')->default(0);
                $table->tinyInteger('mlm_status')->default(0);
                $table->tinyInteger('is_affiliate_user')->default(0);

                // Profile, Credit Limit & Business Fields
                $table->string('profile_photo')->nullable();
                $table->string('avatar')->nullable();
                $table->string('google_id')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->date('dob')->nullable();
                $table->string('payment_qr')->nullable();
                $table->string('business_type')->nullable();
                $table->string('food_type')->nullable();
                $table->enum('duty_status', ['online', 'offline', 'busy'])->default('offline');
                $table->boolean('is_online')->default(false);
                $table->string('vehicle_no')->nullable();
                $table->string('license_no')->nullable();
                $table->string('platform_type', 256)->nullable();
                
                // Credit Limit & Milestone Flow Fields
                $table->decimal('credit_limit', 12, 2)->default(500000.00);
                $table->tinyInteger('credit_limit_progress_percent')->default(0);
                $table->tinyInteger('kyc_stage_completed')->default(0);

                // KYC & Identity Verification
                $table->string('pan_number', 10)->nullable();
                $table->string('pan_image')->nullable();
                $table->string('aadhaar_number', 12)->nullable();
                $table->string('aadhaar_front_image')->nullable();
                $table->string('aadhaar_back_image')->nullable();
                $table->enum('kyc_status', ['pending', 'approved', 'rejected', 'unverified'])->default('unverified');

                // Certificates & Legal
                $table->string('fssai_number')->nullable();
                $table->string('fssai_certificate')->nullable();
                $table->string('gst_certificate')->nullable();
                $table->string('mca_certificate')->nullable();
                $table->string('gstin')->nullable();
                $table->string('upi_qr_code')->nullable();

                // Bank Details
                $table->string('account_holder_name')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('ifsc_code', 20)->nullable();
                $table->string('upi_id')->nullable();

                // Address & Location
                $table->text('address')->nullable();
                $table->string('area')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('pincode', 10)->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();

                // OTP Verification
                $table->string('otp')->nullable();
                $table->timestamp('otp_expires_at')->nullable();

                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Pincodes Table
        if (!Schema::hasTable('pincodes')) {
            Schema::create('pincodes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('circle_name')->nullable();
                $table->string('region_name')->nullable();
                $table->string('division_name')->nullable();
                $table->string('office_name');
                $table->string('pincode', 10)->index();
                $table->string('office_type')->nullable();
                $table->string('delivery_status')->nullable();
                $table->string('district')->nullable();
                $table->string('state_name')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->timestamps();
            });
        }

        // 3. Cache, Jobs & Sessions
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->bigInteger('expiration')->index();
            });
        }

        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedSmallInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // 4. Countries & States
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('country_id')->index();
                $table->string('name');
                $table->string('code');
                $table->timestamps();
            });
        }

        // 5. Wallets & Transactions
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->decimal('real_balance', 12, 2)->default(0.00);
                $table->decimal('non_withdrawable_balance', 12, 2)->default(0.00);
                $table->decimal('t_coins', 12, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendor_wallets')) {
            Schema::create('vendor_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->unique();
                $table->decimal('bonus_balance', 10, 2)->default(200.00);
                $table->decimal('sales_balance', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id');
                $table->enum('wallet_type', ['bonus', 'sales']);
                $table->enum('type', ['credit', 'debit']);
                $table->decimal('amount', 10, 2);
                $table->string('description');
                $table->unsignedBigInteger('order_id')->nullable();
                $table->string('status')->default('success');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('t_coin_transactions')) {
            Schema::create('t_coin_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 14, 2);
                $table->string('type')->comment('credit or debit');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 6. Affiliate & MLM Tables
        if (!Schema::hasTable('affiliate_stages')) {
            Schema::create('affiliate_stages', function (Blueprint $table) {
                $table->id();
                $table->integer('stage_no')->unique();
                $table->string('stage_name');
                $table->integer('leg_a_count');
                $table->integer('leg_b_count');
                $table->decimal('incentive_amount', 12, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_team_stats')) {
            Schema::create('user_team_stats', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->integer('active_leg_a')->default(0);
                $table->integer('inactive_leg_a')->default(0);
                $table->integer('active_leg_b')->default(0);
                $table->integer('inactive_leg_b')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_affiliate_payouts')) {
            Schema::create('user_affiliate_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('stage_id');
                $table->decimal('gross_amount', 12, 2);
                $table->decimal('admin_charge', 12, 2)->default(0.00);
                $table->decimal('tds_amount', 12, 2)->default(0.00);
                $table->decimal('net_amount', 12, 2);
                $table->enum('status', ['locked', 'unlocked', 'paid'])->default('locked');
                $table->timestamps();
            });
        }

        // 7. Taxes, Categories, Units & Brands
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('tax_name');
                $table->decimal('tax_percentage', 5, 2)->default(0.00);
                $table->decimal('cess_percentage', 5, 2)->default(0.00);
                $table->string('tax_type')->default('GST');
                $table->boolean('is_active')->default(true);
                $table->text('remark')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('item_categories')) {
            Schema::create('item_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->tinyInteger('level')->default(1);
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name');
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('brand_name')->unique();
                $table->string('logo')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        // 8. Global Items & Variants
        if (!Schema::hasTable('global_items')) {
            Schema::create('global_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('category');
                $table->unsignedBigInteger('sub_category_id')->nullable();
                $table->unsignedBigInteger('child_category_id')->nullable();
                $table->string('food_type')->default('veg');
                $table->string('item_name');
                $table->string('brand_name')->nullable();
                $table->string('product_summary')->nullable();
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->string('product_type')->default('single');
                $table->decimal('mrp', 10, 2)->nullable();
                $table->decimal('default_price', 10, 2)->default(0);
                $table->unsignedBigInteger('tax_id')->nullable();
                $table->integer('min_order_quantity')->default(1);
                $table->integer('quantity_step_size')->default(1);
                $table->integer('total_allowed_quantity')->default(100);
                $table->boolean('is_cancelable')->default(true);
                $table->boolean('is_returnable')->default(true);
                $table->integer('returnable_days')->default(7);
                $table->boolean('is_inclusive_tax')->default(false);
                $table->boolean('is_attachment_required')->default(false);
                $table->boolean('requires_otp')->default(false);
                $table->text('tags')->nullable();
                $table->json('additional_images')->nullable();
                $table->string('status')->default('1');
                $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
                $table->tinyInteger('is_approved')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('global_item_variants')) {
            Schema::create('global_item_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('global_item_id');
                $table->string('sku')->nullable();
                $table->string('barcode')->nullable();
                $table->string('color')->nullable();
                $table->string('size')->nullable();
                $table->string('variant_name')->nullable();
                $table->decimal('mrp', 10, 2)->default(0);
                $table->decimal('price', 10, 2)->default(0);
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        // 9. Services & Visiting Cards
        if (!Schema::hasTable('tidong_services')) {
            Schema::create('tidong_services', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('service_code')->unique();
                $table->string('name_en');
                $table->string('name_hi');
                $table->string('icon_class');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('visiting_cards')) {
            Schema::create('visiting_cards', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('card_type')->default('atm');
                $table->string('design_type')->nullable();
                $table->string('card_layout')->default('layout_icons');
                $table->unsignedBigInteger('country_id')->nullable();
                $table->unsignedBigInteger('state_id')->nullable();
                $table->string('plan_type')->default('free');
                $table->string('card_no')->unique();
                $table->string('name');
                $table->string('nickname')->nullable();
                $table->tinyInteger('show_nickname')->default(1);
                $table->string('business_name')->nullable();
                $table->string('designation')->nullable();
                $table->tinyInteger('show_designation')->default(1);
                $table->string('tagline')->nullable();
                $table->string('owner_name')->nullable();
                $table->string('phone');
                $table->string('alt_phone')->nullable();
                $table->string('whatsapp')->nullable();
                $table->string('gmail')->nullable();
                $table->string('yahoo_email')->nullable();
                $table->string('other_email')->nullable();
                $table->string('facebook')->nullable();
                $table->string('instagram')->nullable();
                $table->string('twitter_x')->nullable();
                $table->string('linkedin')->nullable();
                $table->string('youtube')->nullable();
                $table->string('telegram')->nullable();
                $table->string('website_link')->nullable();
                $table->string('website')->nullable();
                $table->string('map_location_link')->nullable();
                $table->text('location_url')->nullable();
                $table->string('phonepe')->nullable();
                $table->string('gpay')->nullable();
                $table->string('paytm')->nullable();
                $table->string('upi_id')->nullable();
                $table->text('about_us')->nullable();
                $table->text('services_or_products')->nullable();
                $table->text('other_details')->nullable();
                $table->string('catalog_pdf')->nullable();
                $table->string('photo')->nullable();
                $table->string('qr_code')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('pincode')->nullable();
                $table->string('area')->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                
                // Toggle Fields
                $table->boolean('show_business')->default(true);
                $table->boolean('show_tagline')->default(true);
                $table->boolean('show_phone')->default(true);
                $table->boolean('show_alt_phone')->default(true);
                $table->boolean('show_whatsapp')->default(true);
                $table->boolean('show_gmail')->default(true);
                $table->boolean('show_yahoo_email')->default(true);
                $table->boolean('show_other_email')->default(true);
                $table->boolean('show_facebook')->default(true);
                $table->boolean('show_instagram')->default(true);
                $table->boolean('show_twitter_x')->default(true);
                $table->boolean('show_linkedin')->default(true);
                $table->boolean('show_youtube')->default(true);
                $table->boolean('show_telegram')->default(true);
                $table->boolean('show_website')->default(true);
                $table->boolean('show_phonepe')->default(true);
                $table->boolean('show_gpay')->default(true);
                $table->boolean('show_paytm')->default(true);
                $table->boolean('show_upi')->default(true);
                $table->boolean('show_about_us')->default(true);
                $table->boolean('show_services')->default(true);
                $table->boolean('show_photo')->default(true);
                $table->boolean('show_qr_code')->default(true);
                $table->boolean('show_address')->default(true);
                $table->boolean('show_area')->default(true);
                $table->boolean('show_pincode')->default(true);
                $table->boolean('show_city')->default(true);
                $table->boolean('show_state')->default(true);
                $table->boolean('show_other_details')->default(true);
                $table->boolean('show_location_url')->default(true);
                $table->boolean('show_map')->default(true);
                
                $table->string('profile_photo')->nullable();
                $table->string('banner_image')->nullable();
                $table->string('font_family')->default('Inter');
                $table->string('icon_style')->default('circle');
                $table->string('icon_color')->nullable();
                $table->string('text_color')->nullable();
                $table->timestamps();
            });
        }

        // 10. Restaurant & Custom Items Modules
        if (!Schema::hasTable('restaurant_tables')) {
            Schema::create('restaurant_tables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('table_number');
                $table->integer('seating_capacity')->default(4);
                $table->string('qr_code_token')->nullable()->unique();
                $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('restaurant_categories')) {
            Schema::create('restaurant_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('restaurant_items')) {
            Schema::create('restaurant_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('global_item_id')->constrained('global_items')->onDelete('cascade');
                $table->foreignId('restaurant_category_id')->nullable()->constrained('restaurant_categories')->onDelete('cascade');
                $table->foreignId('tax_id')->nullable()->constrained('taxes')->nullOnDelete();
                $table->decimal('mrp', 10, 2)->default(0);
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_available')->default(true);
                $table->boolean('in_stock')->default(true);
                $table->timestamps();
                $table->unique(['user_id', 'global_item_id']);
            });
        }

        if (!Schema::hasTable('restaurant_custom_items')) {
            Schema::create('restaurant_custom_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('restaurant_category_id')->constrained('restaurant_categories')->onDelete('cascade');
                $table->string('name');
                $table->enum('type', ['veg', 'non-veg', 'egg']);
                $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('set null');
                $table->decimal('mrp', 10, 2);
                $table->decimal('price', 10, 2);
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('restaurant_orders')) {
            Schema::create('restaurant_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null');
                $table->foreignId('delivery_boy_id')->nullable()->constrained('users')->nullOnDelete();
                
                $table->string('order_number')->unique();
                $table->string('order_type')->default('dine_in');
                $table->string('status', 50)->default('pending');
                $table->string('delivery_otp', 6)->nullable();
                $table->decimal('delivery_fee', 8, 2)->default(0.00);
                
                $table->decimal('sub_total', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('cgst', 10, 2)->default(0.00);
                $table->decimal('sgst', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0);
                
                $table->string('payment_status')->default('pending');
                $table->string('payment_method', 50)->nullable()->default('cod');
                
                $table->string('customer_name')->nullable();
                $table->string('customer_phone')->nullable();
                $table->text('delivery_address')->nullable();
                
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // 11. Tiffin Catalogs & Orders Modules
        if (!Schema::hasTable('tiffin_catalogs')) {
            Schema::create('tiffin_catalogs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->string('title');
                $table->string('delivery_address');
                $table->boolean('is_active')->default(true);
                $table->decimal('single_day_rate', 10, 2)->default(0);
                $table->decimal('full_week_rate', 10, 2)->default(0);
                $table->decimal('full_month_rate', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tiffin_catalog_items')) {
            Schema::create('tiffin_catalog_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tiffin_catalog_id')->constrained('tiffin_catalogs')->onDelete('cascade');
                $table->unsignedBigInteger('restaurant_category_id')->nullable();
                $table->string('day');
                $table->string('meal_type');
                $table->json('item_ids')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('proceed_tiffin_orders')) {
            Schema::create('proceed_tiffin_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id');
                $table->string('customer_name')->nullable();
                $table->string('customer_mobile')->nullable();
                $table->string('duration');
                $table->date('from_date')->nullable();
                $table->date('to_date')->nullable();
                $table->text('meal_types')->nullable();
                $table->text('selected_catalogs')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        // 12. Retail Modules
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('delivery_boy_id')->nullable();
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->string('business_type')->nullable(); 
                $table->string('status')->default('pending');
                $table->string('pickup_otp', 6)->nullable();
                $table->string('delivery_otp', 6)->nullable();
                $table->decimal('delivery_fee', 8, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('customer_name')->nullable();
                $table->text('delivery_address')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('retail_categories')) {
            Schema::create('retail_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('retail_products')) {
            Schema::create('retail_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->foreignId('category_id')->constrained('retail_categories')->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('sale_price', 10, 2)->nullable();
                $table->integer('stock')->default(0);
                $table->string('sku')->unique();
                $table->string('status')->default('published');
                $table->timestamps();
            });
        }

        // 13. Taxis & Transport
        if (!Schema::hasTable('taxis')) {
            Schema::create('taxis', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->index();
                $table->string('driver_name')->nullable();
                $table->string('driver_phone')->nullable();
                $table->string('vehicle_name');
                $table->string('vehicle_number');
                $table->enum('vehicle_type', ['Hatchback', 'Sedan', 'SUV', 'Tempo Traveller'])->default('Sedan');
                $table->decimal('per_km_rate', 10, 2)->default(12.00);
                $table->decimal('base_fare', 10, 2)->default(500.00);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('taxi_bookings')) {
            Schema::create('taxi_bookings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('booking_number')->unique();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->text('pickup_address')->nullable();
                $table->text('drop_address')->nullable();
                $table->decimal('total_distance_km', 8, 2)->default(0);
                $table->decimal('grand_total', 10, 2)->default(0);
                $table->enum('booking_status', ['pending', 'confirmed', 'ongoing', 'completed', 'cancelled'])->default('pending');
                $table->timestamps();
            });
        }
        // Transactions Table
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('t_coins', 15, 2)->default(0);
                $table->string('type')->nullable(); // credit / debit
                $table->string('status')->default('success');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
        // Waiter Calls Table
        if (!Schema::hasTable('waiter_calls')) {
            Schema::create('waiter_calls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->onDelete('set null');
                $table->string('call_type')->default('general');
                $table->string('status')->default('pending');
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }
        // Delivery Wallets Table
        if (!Schema::hasTable('delivery_wallets')) {
            Schema::create('delivery_wallets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('balance', 15, 2)->default(0);
                $table->decimal('earning_balance', 15, 2)->default(0); // <-- Yeh column add kiya
                $table->decimal('cash_in_hand', 15, 2)->default(0);
                $table->decimal('total_earned', 15, 2)->default(0);
                $table->decimal('total_withdrawn', 15, 2)->default(0);
                $table->timestamps();
            });
        }
        // 1. Credit Cards Table
        if (!Schema::hasTable('credit_cards')) {
            Schema::create('credit_cards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('card_name'); // e.g. HDFC Regalia, SBI Elite
                $table->string('card_number_last4', 4)->nullable();
                $table->string('bank_name')->nullable();
                $table->decimal('credit_limit', 15, 2)->default(0);
                $table->decimal('available_limit', 15, 2)->default(0);
                $table->integer('statement_date')->nullable(); // e.g. 15th of month
                $table->integer('due_date')->nullable(); // e.g. 5th of next month
                $table->timestamps();
            });
        }

        // 2. Marriage / Event Plans Table
        if (!Schema::hasTable('marriage_plans')) {
            Schema::create('marriage_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('event_title'); // e.g. Gourang's Wedding / Family Wedding
                $table->date('event_date')->nullable();
                $table->decimal('total_budget', 15, 2)->default(0);
                $table->string('city')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Marriage Guests Table
        if (!Schema::hasTable('marriage_guests')) {
            Schema::create('marriage_guests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('marriage_plan_id')->constrained('marriage_plans')->onDelete('cascade');
                $table->string('guest_name');
                $table->string('phone')->nullable();
                $table->string('side')->default('both'); // groom, bride, both
                $table->integer('members_count')->default(1);
                $table->string('invitation_status')->default('pending'); // pending, sent, accepted
                $table->boolean('is_vip')->default(false);
                $table->timestamps();
            });
        }

        // 4. Marriage Expenses Table
        if (!Schema::hasTable('marriage_expenses')) {
            Schema::create('marriage_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('marriage_plan_id')->constrained('marriage_plans')->onDelete('cascade');
                $table->string('item_name'); // e.g. Venue Booking, Catering
                $table->string('category')->nullable(); // Food, Decor, Jewelry, Clothes
                $table->decimal('estimated_cost', 15, 2)->default(0);
                $table->decimal('actual_cost', 15, 2)->default(0);
                $table->string('status')->default('pending'); // pending, paid
                $table->timestamps();
            });
        }
        // Delivery Wallet Transactions Table
        if (!Schema::hasTable('delivery_wallet_transactions')) {
            Schema::create('delivery_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('type')->nullable(); // credit / debit
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('taxi_bookings');
        Schema::dropIfExists('taxis');
        Schema::dropIfExists('retail_products');
        Schema::dropIfExists('retail_categories');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('proceed_tiffin_orders');
        Schema::dropIfExists('tiffin_catalog_items');
        Schema::dropIfExists('tiffin_catalogs');
        Schema::dropIfExists('restaurant_orders');
        Schema::dropIfExists('restaurant_custom_items');
        Schema::dropIfExists('restaurant_items');
        Schema::dropIfExists('restaurant_categories');
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('visiting_cards');
        Schema::dropIfExists('tidong_services');
        Schema::dropIfExists('global_item_variants');
        Schema::dropIfExists('global_items');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('units');
        Schema::dropIfExists('item_categories');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('user_affiliate_payouts');
        Schema::dropIfExists('user_team_stats');
        Schema::dropIfExists('affiliate_stages');
        Schema::dropIfExists('t_coin_transactions');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('vendor_wallets');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('pincodes');
        Schema::dropIfExists('users');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('waiter_calls');
        Schema::dropIfExists('delivery_wallets');
        Schema::dropIfExists('marriage_expenses');
        Schema::dropIfExists('marriage_guests');
        Schema::dropIfExists('marriage_plans');
        Schema::dropIfExists('credit_cards');
        Schema::dropIfExists('delivery_wallet_transactions');
    }
};