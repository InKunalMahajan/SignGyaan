<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('isl_media_asset_id')
                ->nullable()
                ->after('isl_video_url')
                ->constrained('media_assets')
                ->nullOnDelete();
        });

        Schema::table('practice_resources', function (Blueprint $table) {
            $table->foreignId('media_asset_id')
                ->nullable()
                ->after('resource_url')
                ->constrained('media_assets')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('practice_resources', function (Blueprint $table) {
            $table->dropConstrainedForeignId('media_asset_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('isl_media_asset_id');
        });
    }
};
