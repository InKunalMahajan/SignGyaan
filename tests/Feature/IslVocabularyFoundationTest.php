<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IslVocabularyFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vocabulary_term_can_belong_to_subject_course_media_and_multiple_lessons(): void
    {
        [$subject, $course, $unit] = $this->createCourseTree();

        $media = MediaAsset::create([
            'title' => 'Keyboard sign',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/keyboard-sign.mp4',
            'language_code' => 'is',
            'is_published' => true,
        ]);

        $term = VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard',
            'meaning' => 'An input device used to type text and commands.',
            'example' => 'Use the keyboard to enter your name.',
            'isl_media_asset_id' => $media->id,
            'isl_video_url' => 'https://example.com/keyboard-fallback.mp4',
            'sort_order' => 10,
            'is_published' => true,
        ]);

        $firstLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Input Devices',
            'slug' => 'input-devices',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $secondLesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Typing Basics',
            'slug' => 'typing-basics',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $firstLesson->vocabularyTerms()->attach($term->id, ['sort_order' => 2]);
        $secondLesson->vocabularyTerms()->attach($term->id, ['sort_order' => 1]);

        $this->assertTrue($term->subject->is($subject));
        $this->assertTrue($term->course->is($course));
        $this->assertTrue($term->mediaAsset->is($media));
        $this->assertCount(2, $term->lessons);
        $this->assertTrue($firstLesson->fresh()->vocabularyTerms->first()->is($term));
        $this->assertTrue($media->fresh()->vocabularySignUses->first()->is($term));
    }

    public function test_vocabulary_terms_have_a_published_scope_and_lesson_specific_order(): void
    {
        [, , $unit] = $this->createCourseTree();

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Computer Hardware',
            'slug' => 'computer-hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $mouse = VocabularyTerm::create([
            'term' => 'Mouse',
            'slug' => 'mouse',
            'is_published' => true,
        ]);

        $monitor = VocabularyTerm::create([
            'term' => 'Monitor',
            'slug' => 'monitor',
            'is_published' => true,
        ]);

        VocabularyTerm::create([
            'term' => 'Draft Term',
            'slug' => 'draft-term',
            'is_published' => false,
        ]);

        $lesson->vocabularyTerms()->attach([
            $mouse->id => ['sort_order' => 20],
            $monitor->id => ['sort_order' => 10],
        ]);

        $this->assertSame(2, VocabularyTerm::published()->count());
        $this->assertSame(['Monitor', 'Mouse'], $lesson->fresh()->vocabularyTerms->pluck('term')->all());
    }

    public function test_deleting_linked_media_keeps_vocabulary_term_and_deleting_lesson_cleans_pivot(): void
    {
        [, , $unit] = $this->createCourseTree();

        $media = MediaAsset::create([
            'title' => 'Monitor sign',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/monitor.mp4',
            'is_published' => true,
        ]);

        $term = VocabularyTerm::create([
            'term' => 'Monitor',
            'slug' => 'monitor',
            'isl_media_asset_id' => $media->id,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Output Devices',
            'slug' => 'output-devices',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson->vocabularyTerms()->attach($term->id);

        $media->delete();

        $this->assertNull($term->fresh()->isl_media_asset_id);
        $this->assertDatabaseHas('vocabulary_terms', ['id' => $term->id]);

        $lesson->delete();

        $this->assertDatabaseMissing('lesson_vocabulary_term', [
            'lesson_id' => $lesson->id,
            'vocabulary_term_id' => $term->id,
        ]);
        $this->assertDatabaseHas('vocabulary_terms', ['id' => $term->id]);
    }

    private function createCourseTree(): array
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
            'title' => 'Hardware',
            'slug' => 'hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit];
    }
}
