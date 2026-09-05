<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicIslVocabularyLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_library_shows_only_published_vocabulary_from_published_learning_context(): void
    {
        [$subject, $course] = $this->createPublishedContext();

        VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard',
            'meaning' => 'A device used to type text.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Draft Sign',
            'slug' => 'draft-sign',
            'meaning' => 'This should not be public.',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get(route('vocabulary.index'))
            ->assertOk()
            ->assertSee('Keyboard')
            ->assertDontSee('Draft Sign');

        $course->update(['is_published' => false]);

        $this->get(route('vocabulary.index'))
            ->assertOk()
            ->assertDontSee('Keyboard');
    }

    public function test_library_can_search_and_filter_by_subject_and_course(): void
    {
        [$digitalSubject, $digitalCourse] = $this->createPublishedContext();

        $englishSubject = Subject::create([
            'name' => 'English',
            'slug' => 'english',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $englishCourse = Course::create([
            'subject_id' => $englishSubject->id,
            'title' => 'English Basics',
            'slug' => 'english-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        VocabularyTerm::create([
            'subject_id' => $digitalSubject->id,
            'course_id' => $digitalCourse->id,
            'term' => 'Mouse',
            'slug' => 'mouse',
            'meaning' => 'A pointing device for a computer.',
            'example' => 'Use the mouse to select an icon.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        VocabularyTerm::create([
            'subject_id' => $englishSubject->id,
            'course_id' => $englishCourse->id,
            'term' => 'Greeting',
            'slug' => 'greeting',
            'meaning' => 'A word or sign used to greet someone.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get(route('vocabulary.index', ['q' => 'pointing']))
            ->assertOk()
            ->assertSee('Mouse')
            ->assertDontSee('Greeting');

        $this->get(route('vocabulary.index', ['subject' => 'english']))
            ->assertOk()
            ->assertSee('Greeting')
            ->assertDontSee('Mouse');

        $this->get(route('vocabulary.index', ['course' => 'computer-basics']))
            ->assertOk()
            ->assertSee('Mouse')
            ->assertDontSee('Greeting');
    }

    public function test_vocabulary_detail_uses_published_media_and_falls_back_when_media_becomes_draft(): void
    {
        [$subject, $course] = $this->createPublishedContext();

        $media = MediaAsset::create([
            'title' => 'Keyboard ISL Sign',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/keyboard-sign.mp4',
            'caption' => 'Published keyboard sign caption.',
            'is_published' => true,
        ]);

        $term = VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard',
            'meaning' => 'A device used to type text.',
            'isl_media_asset_id' => $media->id,
            'isl_video_url' => 'https://example.com/fallback-keyboard.mp4',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get(route('vocabulary.show', $term->slug))
            ->assertOk()
            ->assertSee('https://example.com/keyboard-sign.mp4', false)
            ->assertSee('Published keyboard sign caption.')
            ->assertDontSee('https://example.com/fallback-keyboard.mp4', false);

        $media->update(['is_published' => false]);

        $this->get(route('vocabulary.show', $term->slug))
            ->assertOk()
            ->assertSee('https://example.com/fallback-keyboard.mp4', false)
            ->assertDontSee('Published keyboard sign caption.');
    }

    private function createPublishedContext(): array
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

        return [$subject, $course];
    }
}
