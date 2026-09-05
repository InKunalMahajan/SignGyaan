<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContentBlock;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseDraftPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_an_unpublished_course_with_draft_units_lessons_and_blocks(): void
    {
        [$admin, $course, $unit, $lesson] = $this->draftFixture();

        LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => 'Draft explanation',
            'body' => 'This block is still being reviewed.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.preview', $course))
            ->assertOk()
            ->assertSee('Admin Preview')
            ->assertSee('Draft course')
            ->assertSee($unit->title)
            ->assertSee($lesson->title)
            ->assertSee(route('admin.courses.preview', ['course' => $course, 'lesson' => 'lesson-'.$lesson->id]), false);

        $this->actingAs($admin)
            ->get(route('admin.courses.preview', ['course' => $course, 'lesson' => 'lesson-'.$lesson->id]))
            ->assertOk()
            ->assertSee('Lesson draft')
            ->assertSee('Draft explanation')
            ->assertSee('This block is still being reviewed.')
            ->assertSee('Draft blocks included');
    }

    public function test_draft_preview_does_not_make_the_course_public(): void
    {
        [$admin, $course] = $this->draftFixture();

        $this->actingAs($admin)
            ->get(route('admin.courses.preview', $course))
            ->assertOk();

        $this->get(route('courses.show', [
            'subject' => $course->subject->slug,
            'course' => $course->slug,
        ]))->assertNotFound();
    }

    public function test_non_admin_cannot_open_draft_preview(): void
    {
        [, $course] = $this->draftFixture();
        $learner = User::factory()->create(['role' => 'learner']);

        $response = $this->actingAs($learner)->get(route('admin.courses.preview', $course));

        $this->assertContains($response->status(), [302, 403]);
    }

    public function test_unknown_lesson_key_returns_not_found_inside_preview(): void
    {
        [$admin, $course] = $this->draftFixture();

        $this->actingAs($admin)
            ->get(route('admin.courses.preview', ['course' => $course, 'lesson' => 'lesson-999999']))
            ->assertNotFound();
    }

    private function draftFixture(): array
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
            'title' => 'Draft Computer Course',
            'slug' => 'draft-computer-course',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Draft Hardware Unit',
            'slug' => 'draft-hardware-unit',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Draft Keyboard Lesson',
            'slug' => 'draft-keyboard-lesson',
            'short_description' => 'Preview this lesson before publishing.',
            'content' => 'Keyboard lesson notes for review.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        return [$admin, $course, $unit, $lesson];
    }
}
