<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContentBlock;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBulkPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bulk_publish_a_ready_course_and_its_managed_content(): void
    {
        [$admin, $course, $unit, $lesson] = $this->readyCourseFixture();

        $block = LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => 'Main idea',
            'body' => 'Learner explanation.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $activity = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quick Practice',
            'slug' => 'quick-practice',
            'kind' => 'practice',
            'resource_type' => 'exercise',
            'content' => 'Try the activity.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $term = VocabularyTerm::create([
            'subject_id' => $course->subject_id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard-publishing-test',
            'meaning' => 'An input device.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.courses.publish-all', $course));

        $response->assertRedirect(route('admin.courses.publishing-checklist', $course));
        $response->assertSessionHas('status');

        $this->assertTrue($course->fresh()->is_published);
        $this->assertTrue($unit->fresh()->is_published);
        $this->assertTrue($lesson->fresh()->is_published);
        $this->assertTrue($block->fresh()->is_published);
        $this->assertTrue($activity->fresh()->is_published);
        $this->assertTrue($term->fresh()->is_published);
    }

    public function test_bulk_publish_is_blocked_when_required_checklist_items_fail(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills-blocked',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Incomplete Course',
            'slug' => 'incomplete-course',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.courses.publish-all', $course));

        $response->assertRedirect(route('admin.courses.publishing-checklist', $course));
        $response->assertSessionHasErrors('publishing');
        $this->assertFalse($course->fresh()->is_published);
    }

    public function test_admin_can_move_the_complete_course_back_to_draft(): void
    {
        [$admin, $course, $unit, $lesson] = $this->readyCourseFixture(true);

        $block = LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'body' => 'Published explanation.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.courses.unpublish-all', $course));

        $response->assertRedirect(route('admin.courses.publishing-checklist', $course));
        $this->assertFalse($course->fresh()->is_published);
        $this->assertFalse($unit->fresh()->is_published);
        $this->assertFalse($lesson->fresh()->is_published);
        $this->assertFalse($block->fresh()->is_published);
    }

    public function test_publishing_page_shows_partial_course_status(): void
    {
        [$admin, $course, $unit] = $this->readyCourseFixture();
        $unit->update(['is_published' => true]);

        $this->actingAs($admin)
            ->get(route('admin.courses.publishing-checklist', $course))
            ->assertOk()
            ->assertSee('Partially published')
            ->assertSee('Publish All')
            ->assertSee('Move All to Draft');
    }

    private function readyCourseFixture(bool $published = false): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Computer Learning',
            'slug' => 'computer-learning-'.uniqid(),
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics-'.uniqid(),
            'level' => 'Beginner',
            'short_description' => 'Learn basic computer concepts step by step.',
            'sort_order' => 1,
            'is_published' => $published,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => $published,
        ]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => 'A computer is an electronic device used to process information.',
            'estimated_duration_minutes' => 10,
            'sort_order' => 1,
            'is_published' => $published,
        ]);

        return [$admin, $course, $unit, $lesson];
    }
}
