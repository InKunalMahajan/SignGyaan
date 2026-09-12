<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                if (! Schema::hasColumn('subjects', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                }

                if (! Schema::hasColumn('subjects', 'description')) {
                    $table->text('description')->nullable();
                }
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (! Schema::hasColumn('courses', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                }

                if (! Schema::hasColumn('courses', 'description')) {
                    $table->text('description')->nullable();
                }

                if (! Schema::hasColumn('courses', 'level')) {
                    $table->string('level', 100)->nullable();
                }
            });
        }

        if (Schema::hasTable('course_units')) {
            Schema::table('course_units', function (Blueprint $table) {
                if (! Schema::hasColumn('course_units', 'is_active')) {
                    $table->boolean('is_active')->default(true)->index();
                }

                if (! Schema::hasColumn('course_units', 'description')) {
                    $table->text('description')->nullable();
                }

                if (! Schema::hasColumn('course_units', 'position')) {
                    $table->unsignedInteger('position')->default(1);
                }
            });
        }

        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (! Schema::hasColumn('lessons', 'summary')) {
                    $table->text('summary')->nullable();
                }

                if (! Schema::hasColumn('lessons', 'isl_video_url')) {
                    $table->string('isl_video_url', 500)->nullable();
                }

                if (! Schema::hasColumn('lessons', 'notes')) {
                    $table->longText('notes')->nullable();
                }

                if (! Schema::hasColumn('lessons', 'estimated_minutes')) {
                    $table->unsignedSmallInteger('estimated_minutes')->nullable();
                }

                if (! Schema::hasColumn('lessons', 'position')) {
                    $table->unsignedInteger('position')->default(1);
                }

                if (! Schema::hasColumn('lessons', 'status')) {
                    $table->string('status', 20)->default('draft')->index();
                }

                if (! Schema::hasColumn('lessons', 'published_at')) {
                    $table->timestamp('published_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Compatibility migration intentionally keeps reconciled columns on rollback
        // to avoid removing columns that may have existed before this migration.
    }
};
