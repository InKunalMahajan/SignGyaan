<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            $table->boolean('is_isl')->default(false)->after('media_type');
            $table->string('language_code', 20)->nullable()->after('caption');
            $table->unsignedInteger('duration_seconds')->nullable()->after('language_code');

            $table->index(['media_type', 'is_isl', 'is_published'], 'media_assets_isl_video_index');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('isl_video_title', 180)->nullable()->after('isl_video_url');
            $table->text('isl_video_caption')->nullable()->after('isl_video_title');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['isl_video_title', 'isl_video_caption']);
        });

        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropIndex('media_assets_isl_video_index');
            $table->dropColumn(['is_isl', 'language_code', 'duration_seconds']);
        });
    }
};
