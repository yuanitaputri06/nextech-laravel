<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Wishlists Table
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('product_id');
                $table->timestamps();

                $table->unique(['user_id', 'product_id']);
                $table->index('user_id');
                $table->index('product_id');
            });
        }

        // 2. Reviews Table
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('product_id');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('review');
                $table->timestamps();

                $table->unique(['user_id', 'product_id']);
                $table->index('product_id');
                $table->index('user_id');
            });
        }

        // 3. User profile_photo column
        if (Schema::hasTable('user_info') && !Schema::hasColumn('user_info', 'profile_photo')) {
            Schema::table('user_info', function (Blueprint $table) {
                $table->string('profile_photo', 255)->nullable()->after('address2');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('wishlists');

        if (Schema::hasTable('user_info') && Schema::hasColumn('user_info', 'profile_photo')) {
            Schema::table('user_info', function (Blueprint $table) {
                $table->dropColumn('profile_photo');
            });
        }
    }
};
