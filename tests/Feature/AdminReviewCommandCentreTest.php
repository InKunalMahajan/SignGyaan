<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\ReviewWorkflowNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReviewCommandCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_review_command_centre_with_live_workload(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$teacher, $course, $unit] = $this->curriculum();

        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Pending Lesson',
            'position' => 1,
            'status' => 'published',
            'review_status' => 'pending',
            'review_submitted_at' => now()->subHour(),
            'published_at' => now(),
        ]);
        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Resubmitted Lesson',
            'position' => 2,
            'status' => 'published',
            'review_status' => 'pending',
            'teacher_response' => 'Updated the ISL explanation.',
            'review_submitted_at' => now(),
            'published_at' => now(),
        ]);
        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Approved Lesson',
            'position' => 3,
            'status' => 'published',
            'review_status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'published_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reviews.dashboard'))
            ->assertOk()
            ->assertSee('Review Queue & Notification Dashboard')
            ->assertSee('Pending Lesson')
            ->assertSee('Resubmitted Lesson')
            ->assertSee('Approved Lesson')
            ->assertSee('Updated the ISL explanation.');
    }

    public function test_resubmission_is_prioritised_before_other_pending_lessons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [, , $unit] = $this->curriculum();

        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Ordinary Pending',
            'position' => 1,
            'status' => 'published',
            'review_status' => 'pending',
            'review_submitted_at' => now()->addMinute(),
            'published_at' => now(),
        ]);
        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Priority Resubmission',
            'position' => 2,
            'status' => 'published',
            'review_status' => 'pending',
            'teacher_response' => 'Fixed requested changes.',
            'review_submitted_at' => now(),
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reviews.dashboard'));
        $response->assertOk();
        $this->assertLessThan(
            strpos($response->getContent(), 'Ordinary Pending'),
            strpos($response->getContent(), 'Priority Resubmission')
        );
    }

    public function test_dashboard_includes_current_admin_unread_notifications_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);
        [$teacher, $course, $unit] = $this->curriculum();
        $lesson = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Alert Lesson',
            'position' => 1,
            'status' => 'published',
            'review_status' => 'pending',
            'published_at' => now(),
        ]);

        $admin->notify(new ReviewWorkflowNotification('review_submitted', 'My Alert', 'Review this lesson.', $lesson, route('admin.curriculum.courses.show', $course)));
        $otherAdmin->notify(new ReviewWorkflowNotification('review_submitted', 'Other Alert', 'Not for this admin.', $lesson, route('admin.curriculum.courses.show', $course)));

        $this->actingAs($admin)
            ->get(route('admin.reviews.dashboard'))
            ->assertOk()
            ->assertSee('My Alert')
            ->assertDontSee('Other Alert');
    }

    public function test_non_admin_cannot_open_review_command_centre(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.reviews.dashboard'))
            ->assertForbidden();
    }

    private function curriculum(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'it-command-centre',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Skills',
            'slug' => 'digital-skills-command-centre',
            'is_active' => true,
        ]);
        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit',
            'position' => 1,
            'is_active' => true,
        ]);

        return [$teacher, $course, $unit];
    }
}
