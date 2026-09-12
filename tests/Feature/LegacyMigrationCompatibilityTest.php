<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacyMigrationCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_migrations_can_be_re_run_against_existing_schema(): void
    {
        $migrationFiles = [
            '2026_09_08_000000_add_role_to_users_table.php',
            '2026_09_08_010000_add_is_active_to_users_table.php',
            '2026_09_08_020000_create_learner_profiles_table.php',
            '2026_09_08_030000_create_parent_profiles_table.php',
            '2026_09_08_031000_create_parent_learner_links_table.php',
            '2026_09_08_040000_create_teacher_profiles_table.php',
            '2026_09_08_041000_create_learning_classes_table.php',
            '2026_09_08_042000_create_class_enrollments_table.php',
            '2026_09_08_050000_create_subjects_table.php',
            '2026_09_08_051000_create_courses_table.php',
            '2026_09_08_052000_create_class_course_assignments_table.php',
            '2026_09_08_060000_create_course_units_table.php',
            '2026_09_08_061000_create_lessons_table.php',
            '2026_09_08_070000_create_lesson_progress_table.php',
            '2026_09_12_020000_create_boards_table.php',
            '2026_09_12_021000_create_academic_classes_table.php',
            '2026_09_12_022000_add_academic_class_id_to_subjects_table.php',
        ];

        foreach ($migrationFiles as $file) {
            $migration = require database_path('migrations/'.$file);
            $migration->up();
        }

        $this->assertTrue(Schema::hasColumn('users', 'role'));
        $this->assertTrue(Schema::hasColumn('users', 'is_active'));
        $this->assertTrue(Schema::hasTable('subjects'));
        $this->assertTrue(Schema::hasTable('courses'));
        $this->assertTrue(Schema::hasTable('boards'));
        $this->assertTrue(Schema::hasTable('academic_classes'));
        $this->assertTrue(Schema::hasColumn('subjects', 'academic_class_id'));
    }

    public function test_legacy_status_is_copied_to_is_active(): void
    {
        if (! Schema::hasColumn('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status', 20)->nullable();
            });
        }

        $activeUser = User::factory()->create(['is_active' => false]);
        $inactiveUser = User::factory()->create(['is_active' => true]);

        $activeUser->forceFill(['status' => 'active'])->save();
        $inactiveUser->forceFill(['status' => 'inactive'])->save();

        $migration = require database_path('migrations/2026_09_08_010000_add_is_active_to_users_table.php');
        $migration->up();

        $this->assertTrue((bool) $activeUser->fresh()->is_active);
        $this->assertFalse((bool) $inactiveUser->fresh()->is_active);
    }
}
