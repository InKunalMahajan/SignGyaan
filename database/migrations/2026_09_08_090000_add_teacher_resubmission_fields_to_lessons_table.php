<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->timestamp('review_submitted_at')->nullable()->after('review_notes');
            $table->text('teacher_response')->nullable()->after('review_submitted_at');
        });

        // Treat existing pending published Lessons as already submitted into the review queue.
        DB::table('lessons')
            ->where('status', 'published')
            ->where('review_status', 'pending')
            ->whereNull('review_submitted_at')
            ->update(['review_submitted_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['review_submitted_at', 'teacher_response']);
        });
    }
};
