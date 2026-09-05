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

class RichLessonContentBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_rich_text_block_to_course_lesson(): void
    {
        [$admin, $subject, $course, $unit, $lesson] = $this->catalogue();

        $response = $this->actingAs($admin)->post(
            route('admin.courses.builder.content-blocks.store', [$course, $lesson]),
            [
                'type' => 'text',
                'title' => 'What is a computer?',
                'body' => 'A computer accepts data, processes it and gives useful output.',
                'is_published' => '1',
            ]
        );

        $block = LessonContentBlock::query()->firstOrFail();

        $response->assertRedirect(route('admin.courses.builder', $course).'#builder-lesson-'.$lesson->id);
        $this->assertSame($lesson->id, $block->lesson_id);
        $this->assertSame('text', $block->type);
        $this->assertSame(1, $block->sort_order);
        $this->assertTrue($block->is_published);
    }

    public function test_text_based_block_requires_body_content(): void
    {
        [$admin, , $course, , $lesson] = $this->catalogue();

        $this->actingAs($admin)
            ->from(route('admin.courses.builder', $course))
            ->post(route('admin.courses.builder.content-blocks.store', [$course, $lesson]), [
                'type' => 'key_points',
                'title' => 'Remember',
                'body' => '',
                'is_published' => '1',
            ])
            ->assertSessionHasErrors('body');

        $this->assertDatabaseCount('lesson_content_blocks', 0);
    }

    public function test_admin_can_reorder_content_blocks_inside_one_lesson(): void
    {
        [$admin, , $course, , $lesson] = $this->catalogue();

        $first = LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'body' => 'First block',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $second = LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'example',
            'body' => 'Second block',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.courses.builder.reorder', $course), [
                'type' => 'content_blocks',
                'parent_id' => $lesson->id,
                'ids' => [$second->id, $first->id],
            ])
            ->assertOk()
            ->assertJson(['saved' => true, 'type' => 'content_blocks', 'count' => 2]);

        $this->assertSame(2, $first->fresh()->sort_order);
        $this->assertSame(1, $second->fresh()->sort_order);
    }

    public function test_public_lesson_shows_only_published_rich_content_blocks(): void
    {
        [, $subject, $course, , $lesson] = $this->catalogue();

        LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => 'Published concept',
            'body' => 'This learner-facing explanation is visible.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => 'Draft concept',
            'body' => 'This content must stay hidden.',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]))
            ->assertOk()
            ->assertSee('Structured lesson content')
            ->assertSee('Published concept')
            ->assertSee('This learner-facing explanation is visible.')
            ->assertDontSee('Draft concept')
            ->assertDontSee('This content must stay hidden.');
    }

    public function test_block_cannot_be_added_to_lesson_from_another_course(): void
    {
        [$admin, , $course] = $this->catalogue();
        [, , , , $otherLesson] = $this->catalogue('second');

        $this->actingAs($admin)
            ->post(route('admin.courses.builder.content-blocks.store', [$course, $otherLesson]), [
                'type' => 'text',
                'body' => 'Wrong course lesson',
                'is_published' => '1',
            ])
            ->assertNotFound();
    }

    private function catalogue(string $suffix = 'main'): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills '.$suffix,
            'slug' => 'digital-skills-'.$suffix,
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics '.$suffix,
            'slug' => 'computer-basics-'.$suffix,
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Computer Foundations '.$suffix,
            'slug' => 'computer-foundations-'.$suffix,
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction '.$suffix,
            'slug' => 'introduction-'.$suffix,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$admin, $subject, $course, $unit, $lesson];
    }
}
