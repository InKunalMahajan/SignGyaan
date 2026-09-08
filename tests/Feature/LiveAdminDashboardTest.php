<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LiveAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_live_platform_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        User::factory()->create(['role' => 'parents']);
        [$class, $course, $unit, $firstLesson] = $this->makeLearningPath($teacher, $learner);
        $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertOk()->assertViewIs('admin.dashboard');
        $response->assertViewHas('totalUsers', 4);
        $response->assertViewHas('activeClassCount', 1);
        $response->assertViewHas('uniqueLearnerCount', 1);
        $response->assertViewHas('activeAssignedCourseCount', 1);
        $response->assertViewHas('activeCurriculumLessonCount', 2);
        $response->assertViewHas('averageCompletion', 50);
        $response->assertSee('Digital Skills');
        $response->assertSee($teacher->name);
    }

    public function test_role_distribution_and_inactive_user_counts_are_live(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'learner']);
        User::factory()->create(['role' => 'teacher', 'is_active' => false]);
        User::factory()->create(['role' => 'parents']);

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertViewHas('totalUsers', 5);
        $response->assertViewHas('inactiveUsers', 1);
        $response->assertViewHas('roleCounts', function ($counts) {
            return $counts['learner'] === 2
                && $counts['teacher'] === 1
                && $counts['parents'] === 1
                && $counts['admin'] === 1;
        });
    }

    public function test_archived_classes_are_visible_but_excluded_from_learning_totals(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        [$class] = $this->makeLearningPath($teacher, $learner);
        $class->update(['is_active' => false]);

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertSee($class->name);
        $response->assertViewHas('totalClasses', 1);
        $response->assertViewHas('activeClassCount', 0);
        $response->assertViewHas('uniqueLearnerCount', 0);
        $response->assertViewHas('activeCurriculumLessonCount', 0);
    }

    public function test_live_curriculum_excludes_draft_hidden_and_inactive_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        [$class, $course, $unit] = $this->makeLearningPath($teacher, $learner);

        Lesson::create(['course_unit_id' => $unit->id, 'title' => 'Draft Lesson', 'position' => 2, 'status' => 'draft']);
        $hiddenUnit = CourseUnit::create(['course_id' => $course->id, 'title' => 'Hidden', 'position' => 2, 'is_active' => false]);
        $this->makeLesson($hiddenUnit, 'Hidden Lesson', 1);

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertViewHas('activeCurriculumLessonCount', 1);
        $response->assertViewHas('publishedLessons', 2);
        $response->assertViewHas('draftLessons', 1);
    }

    public function test_support_signal_uses_below_fifty_percent_threshold_platform_wide(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $below = User::factory()->create(['role' => 'learner', 'name' => 'Below Learner']);
        [$class, $course, $unit] = $this->makeLearningPath($teacher, $below);
        $this->makeLesson($unit, 'Second Lesson', 2);

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertViewHas('needsAttentionLearnerCount', 1);
        $response->assertSee('Below Learner');
    }

    public function test_normal_dashboard_redirect_reaches_live_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/dashboard/admin');
        $this->actingAs($admin)->get('/dashboard/admin')->assertOk()->assertViewIs('admin.dashboard');
    }

    public function test_non_admin_cannot_open_live_admin_dashboard(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher)->get('/dashboard/admin')->assertForbidden();
    }

    private function makeLearningPath(User $teacher, User $learner): array
    {
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Digital Skills',
            'code' => 'SG-ADMIN',
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology-'.Str::lower(Str::random(6)),
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics',
            'slug' => 'digital-basics-'.Str::lower(Str::random(6)),
            'level' => 'Beginner',
            'is_active' => true,
        ]);
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit',
            'position' => 1,
            'is_active' => true,
        ]);
        $lesson = $this->makeLesson($unit, 'Introduction Lesson', 1);

        return [$class, $course, $unit, $lesson];
    }

    private function makeLesson(CourseUnit $unit, string $title, int $position): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => $title.' summary',
            'notes' => $title.' notes',
            'estimated_minutes' => 10,
            'position' => $position,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
