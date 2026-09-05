<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_progress', function (Blueprint $table) {
            $table->json('video_progress')->nullable()->after('completed_lessons');
        });
    }

    public function down(): void
    {
        Schema::table('learning_progress', function (Blueprint $table) {
            $table->dropColumn('video_progress');
        });
    }
};
