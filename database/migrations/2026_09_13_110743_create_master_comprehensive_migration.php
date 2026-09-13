<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Create Consignments Table first
        if (!Schema::hasTable('consignments')) {
            Schema::create('consignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('delivery_boy_id')->nullable();
                $table->string('name', 255)->nullable();
                $table->string('status', 100)->nullable();
                $table->string('active_status', 100)->nullable();
                $table->string('otp', 50)->nullable();
                $table->decimal('delivery_charge', 10, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // 2. Create Consignment Items Table first
        if (!Schema::hasTable('consignment_items')) {
            Schema::create('consignment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('consignment_id');
                $table->unsignedBigInteger('order_item_id');
                $table->unsignedBigInteger('product_variant_id');
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->integer('quantity')->default(1);
                $table->timestamps();

                $table->foreign('consignment_id')->references('id')->on('consignments')->onDelete('cascade');
            });
        }

        // 3. Insert Consignment Data (Only if order_items and orders tables exist)
        if (Schema::hasTable('order_items') && Schema::hasTable('orders')) {
            DB::statement("
                INSERT INTO consignments (order_id, delivery_boy_id, name, status, active_status, otp, delivery_charge, created_at, updated_at)
                SELECT 
                    oi.order_id, 
                    oi.delivery_boy_id, 
                    oi.product_name, 
                    oi.status, 
                    oi.active_status, 
                    oi.otp, 
                    o.delivery_charge AS delivery_charge,  
                    oi.date_added AS created_at,
                    oi.date_added AS updated_at
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                WHERE oi.active_status IN ('processed', 'shipped');
            ");

            DB::statement("
                INSERT INTO consignment_items (consignment_id, order_item_id, product_variant_id, unit_price, quantity, created_at, updated_at)
                SELECT 
                    c.id AS consignment_id, 
                    oi.id AS order_item_id, 
                    oi.product_variant_id, 
                    oi.price AS unit_price, 
                    oi.quantity, 
                    oi.date_added AS created_at, 
                    oi.date_added AS updated_at
                FROM consignments c
                JOIN order_items oi ON oi.order_id = c.order_id
                WHERE oi.active_status IN ('processed', 'shipped');
            ");
        }

        // 4. Add columns to seller_data
        if (Schema::hasTable('seller_data')) {
            Schema::table('seller_data', function (Blueprint $table) {
                if (!Schema::hasColumn('seller_data', 'serviceable_zipcodes')) {
                    if (Schema::hasColumn('seller_data', 'category_ids')) {
                        $table->string('serviceable_zipcodes', 256)->nullable()->after('category_ids');
                    } else {
                        $table->string('serviceable_zipcodes', 256)->nullable();
                    }
                }
                if (!Schema::hasColumn('seller_data', 'serviceable_cities')) {
                    if (Schema::hasColumn('seller_data', 'serviceable_zipcodes')) {
                        $table->string('serviceable_cities', 256)->nullable()->after('serviceable_zipcodes');
                    } else {
                        $table->string('serviceable_cities', 256)->nullable();
                    }
                }
                if (!Schema::hasColumn('seller_data', 'deliverable_zipcode_type')) {
                    if (Schema::hasColumn('seller_data', 'category_ids')) {
                        $table->integer('deliverable_zipcode_type')->nullable()->default(null)->after('category_ids');
                    } else {
                        $table->integer('deliverable_zipcode_type')->nullable()->default(null);
                    }
                }
                if (!Schema::hasColumn('seller_data', 'deliverable_city_type')) {
                    if (Schema::hasColumn('seller_data', 'deliverable_zipcode_type')) {
                        $table->integer('deliverable_city_type')->nullable()->default(null)->after('deliverable_zipcode_type');
                    } else {
                        $table->integer('deliverable_city_type')->nullable()->default(null);
                    }
                }
                if (!Schema::hasColumn('seller_data', 'low_stock_limit')) {
                    if (Schema::hasColumn('seller_data', 'commission')) {
                        $table->integer('low_stock_limit')->default(0)->after('commission');
                    } else {
                        $table->integer('low_stock_limit')->default(0);
                    }
                }
            });
        }

        // 5. Update products brand column structure
        if (Schema::hasTable('products') && Schema::hasTable('brands')) {
            if (Schema::hasColumn('products', 'brand') && !Schema::hasColumn('products', 'brand_id')) {
                Schema::table('products', function (Blueprint $table) {
                    if (Schema::hasColumn('products', 'category_id')) {
                        $table->integer('brand_id')->nullable()->after('category_id');
                    } else {
                        $table->integer('brand_id')->nullable();
                    }
                });

                DB::statement("
                    UPDATE products p
                    JOIN brands b ON p.brand = b.name
                    SET p.brand_id = b.id
                ");

                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('brand');
                });

                Schema::table('products', function (Blueprint $table) {
                    $table->renameColumn('brand_id', 'brand');
                });
            }
        }

        // 6. Add slug to sections and populate
        if (Schema::hasTable('sections')) {
            Schema::table('sections', function (Blueprint $table) {
                if (!Schema::hasColumn('sections', 'slug')) {
                    if (Schema::hasColumn('sections', 'title')) {
                        $table->string('slug', 255)->nullable()->after('title');
                    } else {
                        $table->string('slug', 255)->nullable();
                    }
                }
                if (!Schema::hasColumn('sections', 'seo_page_title')) {
                    if (Schema::hasColumn('sections', 'product_type')) {
                        $table->string('seo_page_title', 1024)->nullable()->default(null)->after('product_type');
                    } else {
                        $table->string('seo_page_title', 1024)->nullable()->default(null);
                    }
                    $table->string('seo_meta_keywords', 10274)->nullable()->default(null);
                    $table->string('seo_meta_description', 1024)->nullable()->default(null);
                    $table->string('seo_og_image', 256)->nullable()->default(null);
                }
            });

            $sections = DB::table('sections')->get();
            foreach ($sections as $record) {
                if (!empty($record->title)) {
                    $slug = strtolower($record->title);
                    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
                    $slug = preg_replace('/[\s-]+/', '-', $slug);
                    DB::table('sections')->where('id', $record->id)->update(['slug' => $slug]);
                }
            }
        }

        // 7. Add SEO fields to categories
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'seo_page_title')) {
                    $table->string('seo_page_title', 1024)->nullable()->default(null);
                    $table->string('seo_meta_keywords', 10274)->nullable()->default(null);
                    $table->string('seo_meta_description', 1024)->nullable()->default(null);
                    $table->string('seo_og_image', 256)->nullable()->default(null);
                }
            });
        }

        // 8. Add final_taxed_price to product_variants
        if (Schema::hasTable('product_variants')) {
            Schema::table('product_variants', function (Blueprint $table) {
                if (!Schema::hasColumn('product_variants', 'final_taxed_price')) {
                    $table->double('final_taxed_price')->default(0);
                }
            });
        }

        // 9. Add platform_type & verification columns to users
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'platform_type')) {
                    if (Schema::hasColumn('users', 'fcm_id')) {
                        $table->string('platform_type', 256)->nullable()->after('fcm_id');
                    } else {
                        $table->string('platform_type', 256)->nullable();
                    }
                }
                if (!Schema::hasColumn('users', 'email_verified')) {
                    if (Schema::hasColumn('users', 'mobile')) {
                        $table->tinyInteger('email_verified')->default(0)->comment('0:not verified, 1:verified')->after('mobile');
                    } else {
                        $table->tinyInteger('email_verified')->default(0)->comment('0:not verified, 1:verified');
                    }
                    $table->tinyInteger('mobile_verified')->default(1)->comment('0:not verified, 1:verified');
                }
                if (!Schema::hasColumn('users', 'is_affiliate_user')) {
                    $table->tinyInteger('is_affiliate_user')->default(0);
                }
            });

            if (Schema::hasColumn('users', 'platform_type')) {
                DB::statement("
                    UPDATE users
                    SET platform_type = 'ios'
                    WHERE platform_type IS NULL OR platform_type = '';
                ");
            }

            if (Schema::hasColumn('users', 'type') && Schema::hasColumn('users', 'email_verified')) {
                DB::table('users')->whereIn('type', ['google', 'ios'])->update(['email_verified' => 1]);
            }
            if (Schema::hasColumn('users', 'type') && Schema::hasColumn('users', 'mobile_verified')) {
                DB::table('users')->where('type', 'google')->update(['mobile_verified' => 0]);
            }
        }

        // 10. Create welcome_wallet_users table
        if (!Schema::hasTable('welcome_wallet_users')) {
            Schema::create('welcome_wallet_users', function (Blueprint $table) {
                $table->id();
                $table->string('email', 100)->nullable();
                $table->string('type', 100)->nullable();
                $table->string('mobile', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });

            if (Schema::hasTable('users') && Schema::hasColumn('users', 'type') && Schema::hasColumn('users', 'email') && Schema::hasColumn('users', 'mobile')) {
                $emails = DB::table('users')->select('type', 'email', 'mobile')->where('type', 'google')->get();
                if ($emails->count() > 0) {
                    DB::table('welcome_wallet_users')->insert(json_decode(json_encode($emails), true));
                }
            }
        }

        // 11. Add columns to order_items
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'product_type')) {
                    $table->string('product_type', 256)->nullable()->default(null);
                    $table->string('product_image', 256)->nullable()->default(null);
                    $table->integer('product_is_cancelable')->nullable()->default(null);
                    $table->integer('product_is_returnable')->nullable()->default(null);
                }
                if (!Schema::hasColumn('order_items', 'affiliate_id')) {
                    $table->unsignedBigInteger('affiliate_id')->nullable()->default(null);
                    $table->string('affiliate_token', 255)->nullable()->default(null);
                    $table->decimal('affiliate_commission', 10, 2)->nullable()->default(0.00);
                    $table->decimal('affiliate_commission_amount', 10, 2)->nullable()->default(0.00);
                    $table->tinyInteger('is_affiliate_commission_settled')->default(0);
                }
                if (!Schema::hasColumn('order_items', 'return_reason')) {
                    $table->string('return_reason', 256)->nullable()->default(null);
                    $table->string('return_item_image', 256)->nullable()->default(null);
                }
            });
        }

        // 12. Insert Affiliate Group
        if (Schema::hasTable('groups')) {
            DB::table('groups')->updateOrInsert(
                ['id' => 5],
                ['name' => 'affiliate', 'description' => 'Affiliate Users']
            );
        }

        // 13. Create Affiliates Table
        if (!Schema::hasTable('affiliates')) {
            Schema::create('affiliates', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('website_url', 255)->nullable();
                $table->string('mobile_app_url', 255)->nullable();
                $table->tinyInteger('status')->default(0)->comment('0 = pending, 1 = approved, 2 = rejected');
                $table->string('affiliate_wallet_balance', 255)->default(0);
                $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('default_commission_rate', 10, 2)->nullable()->default('0.00');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->useCurrent();
                $table->timestamp('deleted_at')->useCurrent()->nullable();
            });
        }

        // 14. Create Affiliate Tracking Table
        if (!Schema::hasTable('affiliate_tracking')) {
            Schema::create('affiliate_tracking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('affiliate_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->double('category_commission', 10, 2)->unsigned()->nullable()->default(0.00);
                $table->string('token', 255)->unique();
                $table->integer('usage_count')->default(0)->comment('token used count');
                $table->decimal('commission_earned', 10, 2)->default(0.00);
                $table->decimal('total_order_value', 10, 2)->default(0.00);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('revoked_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        // 15. Create Affiliate Wallet Transactions Table
        if (!Schema::hasTable('affiliate_wallet_transactions')) {
            Schema::create('affiliate_wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->enum('type', ['credit', 'debit'])->default('credit');
                $table->string('reference_type', 128)->default('credit')->comment('order(get commission), withdraw(withdrawal amount)');
                $table->text('message')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        // 16. Create Zipcode Groups Tables
        if (!Schema::hasTable('zipcode_groups')) {
            Schema::create('zipcode_groups', function (Blueprint $table) {
                $table->id();
                $table->string('group_name', 255);
                $table->decimal('delivery_charges', 10, 2)->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('zipcode_group_items')) {
            Schema::create('zipcode_group_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->integer('zipcode_id');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();

                $table->foreign('group_id')->references('id')->on('zipcode_groups')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // 17. Create City Groups Tables
        if (!Schema::hasTable('city_groups')) {
            Schema::create('city_groups', function (Blueprint $table) {
                $table->id();
                $table->string('group_name', 255);
                $table->decimal('delivery_charges', 10, 2)->default(0);
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
            });
        }

        if (!Schema::hasTable('city_group_items')) {
            Schema::create('city_group_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->integer('city_id');
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
                $table->index('group_id');
            });
        }

        // 18. Add Deliverable & Low Stock Groups to Products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'deliverable_group_type')) {
                    $table->integer('deliverable_group_type')->default(1)->comment('(0:none, 1:all, 2:include, 3:exclude)');
                    $table->string('deliverable_zipcodes_group', 512)->nullable()->default(null);
                    $table->integer('deliverable_city_group_type')->default(1)->comment('(0:none, 1:all, 2:include, 3:exclude)');
                    $table->string('deliverable_cities_group', 512)->nullable()->default(null);
                }
                if (!Schema::hasColumn('products', 'low_stock_limit')) {
                    $table->integer('low_stock_limit')->default(0);
                }
            });
        }

        // 19. Add email column to otps
        if (Schema::hasTable('otps')) {
            Schema::table('otps', function (Blueprint $table) {
                if (!Schema::hasColumn('otps', 'email')) {
                    $table->string('email', 255)->nullable();
                }
            });
        }

        // 20. Create return_reasons table
        if (!Schema::hasTable('return_reasons')) {
            Schema::create('return_reasons', function (Blueprint $table) {
                $table->id();
                $table->string('return_reason', 100)->nullable();
                $table->string('message', 100)->nullable();
                $table->string('image', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        // 21. Add columns to return_requests
        if (Schema::hasTable('return_requests')) {
            Schema::table('return_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('return_requests', 'return_reason')) {
                    $table->string('return_reason', 256)->nullable()->default(null);
                    $table->string('return_item_image', 256)->nullable()->default(null);
                }
            });
        }

        // 22. Add columns to user_fcm
        if (Schema::hasTable('user_fcm')) {
            Schema::table('user_fcm', function (Blueprint $table) {
                if (!Schema::hasColumn('user_fcm', 'user_id')) {
                    $table->integer('user_id')->nullable()->default(null);
                }
                if (!Schema::hasColumn('user_fcm', 'platform_type')) {
                    $table->string('platform_type', 256)->default('ios');
                }
            });
        }

        // 23. Update low stock limit for seller_data
        if (Schema::hasTable('seller_data') && Schema::hasColumn('seller_data', 'low_stock_limit')) {
            DB::statement("UPDATE `seller_data` SET `low_stock_limit` = 5");
        }
    }

    public function down()
    {
        // Rollback code can be defined here if necessary
    }
};