<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTeacherOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_teacher_overview_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create([
            'name' => 'Assigned Teacher',
            'last_login_at' => now(),
        ]);
        $unassignedTeacher = User::factory()->teacher()->create([
            'name' => 'Unassigned Teacher',
        ]);

        TeacherProfile::query()->create([
            'user_id' => $teacher->id,
            'employee_code' => 'T-001',
            'qualification' => 'B.Ed.',
            'specialization' => 'Mathematics',
            'experience_years' => 5,
        ]);

        $subject = Subject::factory()->create();
        $course = Course::factory()->create(['subject_id' => $subject->id]);

        $teacher->teachingSubjects()->attach($subject->id);
        $teacher->teachingCourses()->attach($course->id);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Teacher Overview Dashboard')
            ->assertSee('Total teachers')
            ->assertSee('Assigned teachers')
            ->assertSee('Subject assignments')
            ->assertSee('Course assignments')
            ->assertSee('Teacher Account Health')
            ->assertSee('Profile & Assignment Coverage')
            ->assertSee('Teacher Assignment Snapshot')
            ->assertSee('Assigned Teacher')
            ->assertSee('Unassigned Teacher')
            ->assertSee('Mathematics')
            ->assertSee('T-001');
    }

    public function test_teacher_overview_contains_accessible_assignment_progress_indicators(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->teacher()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('aria-label="Teacher professional profile coverage"', false)
            ->assertSee('aria-label="Teacher assignment coverage"', false)
            ->assertSee('role="progressbar"', false);
    }
}
