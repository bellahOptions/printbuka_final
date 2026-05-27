<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds cloudinary_public_id columns to track the Cloudinary resource ID
     * for each image across all image-hosting tables.
     */
    public function up(): void
    {
        // Products – featured image
        Schema::table('products', function (Blueprint $table) {
            $table->string('featured_image_cloudinary_id')->nullable()->after('featured_image');
        });

        // Blog posts – featured image
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('featured_image_cloudinary_id')->nullable()->after('featured_image');
        });

        // Users (staff & customers) – profile photo
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_cloudinary_id')->nullable()->after('photo');
        });

        // Product categories – category image
        if (Schema::hasColumn('product_categories', 'image')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->string('image_cloudinary_id')->nullable()->after('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('featured_image_cloudinary_id');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('featured_image_cloudinary_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_cloudinary_id');
        });

        if (Schema::hasColumn('product_categories', 'image_cloudinary_id')) {
            Schema::table('product_categories', function (Blueprint $table) {
                $table->dropColumn('image_cloudinary_id');
            });
        }
    }
};
