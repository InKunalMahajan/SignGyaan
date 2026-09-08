<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\ParentLearnerLink;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveParentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_dashboard_shows_only_approved_linked_learners(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $approvedLearner = User::factory()->create(['role' => 'learner', 'name' => 'Approved Learner']);
        $pendingLearner = User::factory()->create(['role' => 'learner', 'name' => 'Pending Learner']);

        $this->link($parent, $approvedLearner, 'approved');
        $this->link($parent, $pendingLearner, 'pending');

        $response = $this->actingAs($parent)->get('/dashboard/parents');

        $response->assertOk();
        $response->assertViewIs('parents.dashboard');
        $response->assertSee('Approved Learner');
        $response->assertDontSee('Pending Learner');
        $response->assertSee('1 pending request');
    }

    public function test_parent_dashboard_uses_real_published_lesson_progress(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner', 'name' => 'Kiran Learner']);
        $this->link($parent, $learner, 'approved');

        [$class, $course, $unit] = $this->learningPath($teacher, $learner);
        $completedLesson = $this->lesson($unit, 'Completed Lesson', 'published', 1);
        $this->lesson($unit, 'Next Lesson', 'published', 2);
        $this->lesson($unit, 'Draft Lesson', 'draft', 3);

        $hiddenUnit = $course->units()->create([
            'title' => 'Hidden Unit',
            'position' => 2,
            'is_active' => false,
        ]);
        $this->lesson($hiddenUnit, 'Hidden Lesson', 'published', 1);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $completedLesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($parent)->get('/dashboard/parents');
        $cards = $response->viewData('learnerCards');
        $progress = $cards->first()['progress'];

        $response->assertOk();
        $this->assertSame(1, $progress['active_class_count']);
        $this->assertSame(1, $progress['course_count']);
        $this->assertSame(1, $progress['completed_lesson_count']);
        $this->assertSame(2, $progress['total_lesson_count']);
        $this->assertSame(50, $progress['overall_percent']);
        $response->assertSee('50%');
        $response->assertDontSee('Draft Lesson');
        $response->assertDontSee('Hidden Lesson');
    }

    public function test_approved_learner_detail_shows_live_course_and_recent_activity(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner', 'name' => 'Live Progress Learner']);
        $link = $this->link($parent, $learner, 'approved');

        [, $course, $unit] = $this->learningPath($teacher, $learner, 'Digital Skills');
        $lesson = $this->lesson($unit, 'Storage Devices', 'published', 1);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(30),
            'last_viewed_at' => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($parent)->get(route('parents.learners.show', $link));

        $response->assertOk();
        $response->assertSee('Live Progress Learner');
        $response->assertSee('Digital Skills');
        $response->assertSee('Storage Devices');
        $response->assertSee('In progress');
    }

    public function test_pending_or_unowned_link_cannot_expose_learner_progress(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $otherParent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create(['role' => 'learner']);
        $pending = $this->link($parent, $learner, 'pending');

        $this->actingAs($parent)
            ->get(route('parents.learners.show', $pending))
            ->assertForbidden();

        $approved = $this->link($parent, $learner, 'approved');

        $this->actingAs($otherParent)
            ->get(route('parents.learners.show', $approved))
            ->assertForbidden();
    }

    public function test_reusable_course_is_counted_once_for_same_learner_across_classes(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $this->link($parent, $learner, 'approved');

        [$firstClass, $course, $unit] = $this->learningPath($teacher, $learner, 'Shared Course');
        $this->lesson($unit, 'Shared Lesson', 'published', 1);

        $secondClass = $this->makeClass($teacher, 'Second Class');
        $secondClass->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $secondClass->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($parent)->get('/dashboard/parents');
        $progress = $response->viewData('learnerCards')->first()['progress'];

        $this->assertSame(2, $progress['total_class_count']);
        $this->assertSame(1, $progress['course_count']);
        $this->assertSame(1, $progress['total_lesson_count']);
    }

    public function test_parent_dashboard_has_safe_empty_state_without_approved_links(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);

        $response = $this->actingAs($parent)->get('/dashboard/parents');

        $response->assertOk();
        $response->assertSee('No approved Learner links yet');
        $this->assertSame(0, $response->viewData('approvedLearnerCount'));
        $this->assertSame(0, $response->viewData('averageProgress'));
    }

    public function test_dashboard_redirect_for_parent_reaches_live_parent_dashboard(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);

        $this->actingAs($parent)
            ->get('/dashboard')
            ->assertRedirect(route('dashboard.role', ['role' => 'parents']));

        $this->actingAs($parent)
            ->get('/dashboard/parents')
            ->assertOk()
            ->assertViewIs('parents.dashboard');
    }

    public function test_non_parent_cannot_open_live_parent_dashboard(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get('/dashboard/parents')
            ->assertForbidden();
    }

    private function link(User $parent, User $learner, string $status): ParentLearnerLink
    {
        return ParentLearnerLink::updateOrCreate(
            [
                'parent_user_id' => $parent->id,
                'learner_user_id' => $learner->id,
            ],
            [
                'relationship' => 'parent',
                'status' => $status,
                'responded_at' => $status === 'pending' ? null : now(),
                'approved_at' => $status === 'approved' ? now() : null,
            ]
        );
    }

    private function learningPath(User $teacher, User $learner, string $courseTitle = 'Digital Basics'): array
    {
        $class = $this->makeClass($teacher, 'FYJC IT');
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::firstOrCreate(
            ['slug' => 'information-technology'],
            [
                'created_by' => $teacher->id,
                'name' => 'Information Technology',
                'is_active' => true,
            ]
        );

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => $courseTitle,
            'slug' => 'course-'.strtolower(substr(md5($courseTitle.$learner->id), 0, 12)),
            'level' => 'Beginner',
            'description' => 'Accessible learning course',
            'is_active' => true,
        ]);

        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $unit = $course->units()->create([
            'title' => 'Unit 1',
            'position' => 1,
            'is_active' => true,
        ]);

        return [$class, $course, $unit];
    }

    private function lesson($unit, string $title, string $status, int $position): Lesson
    {
        return $unit->lessons()->create([
            'title' => $title,
            'summary' => $title.' summary',
            'position' => $position,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }

    private function makeClass(User $teacher, string $name): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'code' => 'SG-'.strtoupper(substr(md5($name.$teacher->id), 0, 6)),
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
    }
}
