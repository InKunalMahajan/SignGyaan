<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('simplified_summary')->nullable()->after('short_description');
            $table->longText('isl_transcript')->nullable()->after('isl_video_caption');
            $table->longText('key_vocabulary')->nullable()->after('isl_transcript');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn([
                'simplified_summary',
                'isl_transcript',
                'key_vocabulary',
            ]);
        });
    }
};
