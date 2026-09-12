<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons') || ! Schema::hasColumn('lessons', 'unit_id')) {
            return;
        }

        // Older SignGyaan databases used `unit_id` as the required lesson-to-unit
        // relationship. Current code uses `course_unit_id`. Keep the legacy column
        // for existing data, but make it nullable so new lessons can be created
        // using only the current relationship.
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally non-destructive. Restoring NOT NULL could fail for lessons
        // created by the current schema where the legacy `unit_id` is null.
    }
};
