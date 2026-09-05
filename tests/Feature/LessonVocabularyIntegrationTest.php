<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonVocabularyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_link_vocabulary_to_a_lesson_and_public_lesson_shows_only_published_linked_terms(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$subject, $course, $unit, $lesson] = $this->createPublishedCourseTree();

        $this->actingAs($admin)
            ->post(route('admin.vocabulary.store'), [
                'subject_id' => $subject->id,
                'course_id' => $course->id,
                'term' => 'Keyboard',
                'meaning' => 'An input device used for typing.',
                'isl_video_url' => 'https://example.com/keyboard-sign.mp4',
                'lesson_ids' => [$lesson->id],
                'sort_order' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.vocabulary.index'));

        $publishedTerm = VocabularyTerm::query()->where('slug', 'keyboard')->firstOrFail();

        $this->assertDatabaseHas('lesson_vocabulary_term', [
            'lesson_id' => $lesson->id,
            'vocabulary_term_id' => $publishedTerm->id,
            'sort_order' => 0,
        ]);

        $draftTerm = VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Hidden Sign',
            'slug' => 'hidden-sign',
            'meaning' => 'This draft term must not be public.',
            'sort_order' => 2,
            'is_published' => false,
        ]);
        $draftTerm->lessons()->attach($lesson->id, ['sort_order' => 1]);

        $url = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('Signs used in this lesson')
            ->assertSee('Keyboard')
            ->assertSee('An input device used for typing.')
            ->assertSee(route('vocabulary.show', 'keyboard'), false)
            ->assertSee('Video available')
            ->assertDontSee('Hidden Sign')
            ->assertDontSee('This draft term must not be public.');
    }

    public function test_course_specific_term_is_not_shown_on_a_mismatched_lesson_even_if_pivot_is_linked(): void
    {
        [$subject, $course, $unit, $lesson] = $this->createPublishedCourseTree();

        $otherCourse = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Advanced Computers',
            'slug' => 'advanced-computers',
            'level' => 'Advanced',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $term = VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $otherCourse->id,
            'term' => 'Server Rack',
            'slug' => 'server-rack',
            'meaning' => 'A rack for server equipment.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $term->lessons()->attach($lesson->id, ['sort_order' => 0]);

        $url = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertDontSee('Server Rack')
            ->assertDontSee('A rack for server equipment.');
    }

    private function createPublishedCourseTree(): array
    {
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
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Input Devices',
            'slug' => 'input-devices',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Using a Keyboard',
            'slug' => 'using-a-keyboard',
            'short_description' => 'Learn how a keyboard is used.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lesson];
    }
}
