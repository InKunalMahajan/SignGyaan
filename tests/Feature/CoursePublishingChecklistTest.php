<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Services\Admin\CoursePublishingChecklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePublishingChecklistTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_review_required_and_recommended_publishing_checks(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.publishing-checklist', $course))
            ->assertOk()
            ->assertSee('Publishing Checklist')
            ->assertSee('required checks need attention')
            ->assertSee('Subject is published')
            ->assertSee('Course description is ready')
            ->assertSee('Course has units and lessons')
            ->assertSee('Every unit contains a lesson')
            ->assertSee('Accessibility & learner experience');
    }

    public function test_course_is_ready_when_all_required_checks_pass(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'short_description' => 'Learn the foundations of using a computer.',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => 'A computer is an electronic device used to process information.',
            'estimated_duration_minutes' => 10,
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $result = app(CoursePublishingChecklist::class)->evaluate($course);

        $this->assertTrue($result['ready']);
        $this->assertCount(0, $result['blockers']);

        $this->actingAs($admin)
            ->get(route('admin.courses.publishing-checklist', $course))
            ->assertOk()
            ->assertSee('Required checks are complete')
            ->assertSee('0</p><p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-sign-muted">Blockers', false)
            ->assertSee('Ready to publish')
            ->assertSee('Publish All')
            ->assertSee('Preview Course');
    }

    public function test_non_admin_cannot_access_the_publishing_checklist(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $this->actingAs($learner)
            ->get(route('admin.courses.publishing-checklist', $course))
            ->assertForbidden();
    }

    public function test_course_edit_page_links_to_publishing_checklist(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.edit', $course))
            ->assertOk()
            ->assertSee('Publishing Checklist')
            ->assertSee(route('admin.courses.publishing-checklist', $course), false);
    }
}
