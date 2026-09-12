<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        if (! Schema::hasColumn('lessons', 'course_unit_id')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->unsignedBigInteger('course_unit_id')->nullable()->after('id')->index();
            });
        }

        // Some older SignGyaan databases used `unit_id` for this relationship.
        // Preserve those links when upgrading to the current CourseUnit model.
        if (Schema::hasColumn('lessons', 'unit_id')) {
            DB::table('lessons')
                ->whereNull('course_unit_id')
                ->whereNotNull('unit_id')
                ->update(['course_unit_id' => DB::raw('unit_id')]);
        }

        if (! Schema::hasColumn('lessons', 'title')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->string('title', 180)->nullable()->after('course_unit_id');
            });
        }

        if (! Schema::hasColumn('lessons', 'created_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasColumn('lessons', 'updated_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Intentionally non-destructive. These columns may represent legacy data
        // that existed before Laravel recorded this compatibility migration.
    }
};
