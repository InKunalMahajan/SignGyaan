<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentOption;
use App\Models\AssessmentQuestion;
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

class CourseBuilderDuplicateContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_duplicate_a_complete_lesson_as_draft(): void
    {
        [$admin, $course, $unit, $lesson] = $this->fixture();

        $practice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Check your understanding',
            'slug' => 'check-your-understanding',
            'kind' => 'practice',
            'resource_type' => 'quiz',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $assessment = Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 60,
            'is_published' => true,
        ]);
        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which device is used to type?',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);
        AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Keyboard',
            'is_correct' => true,
            'sort_order' => 1,
        ]);
        $block = LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'practice',
            'title' => 'Try it now',
            'practice_resource_id' => $practice->id,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.courses.builder.duplicate-lesson', [$course, $lesson])
        );

        $copy = Lesson::query()->where('unit_id', $unit->id)->where('id', '!=', $lesson->id)->firstOrFail();
        $copiedPractice = $copy->practiceResources()->firstOrFail();
        $copiedBlock = $copy->contentBlocks()->firstOrFail();
        $copiedAssessment = $copiedPractice->assessment()->firstOrFail();

        $this->assertSame('Copy of '.$lesson->title, $copy->title);
        $this->assertFalse($copy->is_published);
        $this->assertFalse($copiedPractice->is_published);
        $this->assertFalse($copiedBlock->is_published);
        $this->assertSame($copiedPractice->id, $copiedBlock->practice_resource_id);
        $this->assertNotSame($practice->id, $copiedBlock->practice_resource_id);
        $this->assertFalse($copiedAssessment->is_published);
        $this->assertCount(1, $copiedAssessment->questions);
        $this->assertCount(1, $copiedAssessment->questions->first()->options);
        $this->assertSame($block->media_asset_id, $copiedBlock->media_asset_id);

        $response->assertRedirect(route('admin.courses.builder', $course).'#builder-lesson-'.$copy->id);
    }

    public function test_admin_can_duplicate_a_unit_with_all_nested_lessons(): void
    {
        [$admin, $course, $unit, $lesson] = $this->fixture();

        LessonContentBlock::create([
            'lesson_id' => $lesson->id,
            'type' => 'text',
            'title' => 'Explanation',
            'body' => 'A keyboard is an input device.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.courses.builder.duplicate-unit', [$course, $unit]))
            ->assertRedirect();

        $copy = Unit::query()->where('course_id', $course->id)->where('id', '!=', $unit->id)->firstOrFail();
        $copiedLesson = $copy->lessons()->firstOrFail();

        $this->assertSame('Copy of '.$unit->title, $copy->title);
        $this->assertFalse($copy->is_published);
        $this->assertSame($lesson->title, $copiedLesson->title);
        $this->assertFalse($copiedLesson->is_published);
        $this->assertCount(1, $copiedLesson->contentBlocks);
        $this->assertFalse($copiedLesson->contentBlocks->first()->is_published);
    }

    public function test_admin_can_duplicate_a_complete_course_and_remap_course_vocabulary(): void
    {
        [$admin, $course, $unit, $lesson] = $this->fixture();

        $term = VocabularyTerm::create([
            'subject_id' => $course->subject_id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard',
            'meaning' => 'An input device used for typing.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson->vocabularyTerms()->attach($term->id, ['sort_order' => 1]);

        $response = $this->actingAs($admin)
            ->post(route('admin.courses.builder.duplicate-course', $course));

        $copy = Course::query()->where('subject_id', $course->subject_id)->where('id', '!=', $course->id)->firstOrFail();
        $copiedUnit = $copy->units()->firstOrFail();
        $copiedLesson = $copiedUnit->lessons()->firstOrFail();
        $copiedTerm = $copy->vocabularyTerms()->firstOrFail();

        $this->assertSame('Copy of '.$course->title, $copy->title);
        $this->assertFalse($copy->is_published);
        $this->assertFalse($copy->is_featured);
        $this->assertSame($unit->title, $copiedUnit->title);
        $this->assertSame($lesson->title, $copiedLesson->title);
        $this->assertFalse($copiedUnit->is_published);
        $this->assertFalse($copiedLesson->is_published);
        $this->assertFalse($copiedTerm->is_published);
        $this->assertNotSame($term->id, $copiedTerm->id);
        $this->assertTrue($copiedLesson->vocabularyTerms->contains($copiedTerm));
        $this->assertFalse($copiedLesson->vocabularyTerms->contains($term));

        $response->assertRedirect(route('admin.courses.builder', $copy));
    }

    public function test_duplicate_controls_script_and_admin_layout_are_available(): void
    {
        [$admin, $course] = $this->fixture();

        $this->actingAs($admin)
            ->get(route('admin.courses.builder', $course))
            ->assertOk()
            ->assertSee('admin-course-builder-duplicate.js', false);

        $script = file_get_contents(public_path('js/admin-course-builder-duplicate.js'));
        $this->assertIsString($script);
        $this->assertStringContainsString('Copy Course', $script);
        $this->assertStringContainsString('Copy Unit', $script);
        $this->assertStringContainsString('/duplicate', $script);
    }

    private function fixture(): array
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
            'is_featured' => true,
            'is_published' => true,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Computer Hardware',
            'slug' => 'computer-hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Keyboard Basics',
            'slug' => 'keyboard-basics',
            'content' => 'Learn how a keyboard works.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$admin, $course, $unit, $lesson];
    }
}
