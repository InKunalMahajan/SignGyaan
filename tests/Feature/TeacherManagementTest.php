<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_teacher_management_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create(['name' => 'Teacher One']);
        User::factory()->learner()->create(['name' => 'Learner One']);

        $this->actingAs($admin)
            ->get(route('admin.teachers.index'))
            ->assertOk()
            ->assertSee('Teacher Management')
            ->assertSee($teacher->name)
            ->assertDontSee('Learner One');
    }

    public function test_admin_can_update_teacher_profile_and_assign_subjects_and_courses(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();

        $subject = Subject::query()->create([
            'name' => 'Information Technology',
            'slug' => 'information-technology',
            'short_description' => 'IT learning',
            'description' => 'Information Technology',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::query()->create([
            'subject_id' => $subject->id,
            'title' => 'Digital Skills',
            'slug' => 'digital-skills',
            'level' => 'Beginner',
            'short_description' => 'Digital skills course',
            'description' => 'Digital skills course',
            'estimated_duration_minutes' => 60,
            'sort_order' => 1,
            'is_featured' => false,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.teachers.update', $teacher), [
                'employee_code' => 'T-001',
                'qualification' => 'B.Ed.',
                'specialization' => 'Information Technology',
                'experience_years' => 5,
                'bio' => 'Teacher profile text.',
                'subjects' => [$subject->id],
                'courses' => [$course->id],
            ])
            ->assertRedirect(route('admin.teachers.edit', $teacher));

        $this->assertDatabaseHas('teacher_profiles', [
            'user_id' => $teacher->id,
            'employee_code' => 'T-001',
            'experience_years' => 5,
        ]);

        $this->assertDatabaseHas('subject_teacher', [
            'user_id' => $teacher->id,
            'subject_id' => $subject->id,
        ]);

        $this->assertDatabaseHas('course_teacher', [
            'user_id' => $teacher->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_non_teacher_cannot_be_opened_in_teacher_manager(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->get(route('admin.teachers.edit', $learner))
            ->assertNotFound();
    }
}
