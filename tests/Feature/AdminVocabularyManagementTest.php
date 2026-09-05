<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\User;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVocabularyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_filter_and_delete_vocabulary_terms(): void
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
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $video = MediaAsset::create([
            'uploaded_by' => $admin->id,
            'title' => 'Keyboard ISL Sign',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/keyboard.mp4',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.vocabulary.store'), [
                'subject_id' => $subject->id,
                'course_id' => $course->id,
                'term' => 'Keyboard',
                'meaning' => 'An input device used for typing.',
                'example' => 'Use the keyboard to type your name.',
                'isl_media_asset_id' => $video->id,
                'sort_order' => 2,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.vocabulary.index'));

        $term = VocabularyTerm::query()->firstOrFail();
        $this->assertSame('keyboard', $term->slug);
        $this->assertSame($subject->id, $term->subject_id);
        $this->assertSame($course->id, $term->course_id);
        $this->assertSame($video->id, $term->isl_media_asset_id);
        $this->assertTrue($term->is_published);

        $this->actingAs($admin)
            ->get(route('admin.vocabulary.index', ['q' => 'Keyboard', 'status' => 'published', 'video' => 'with']))
            ->assertOk()
            ->assertSee('Keyboard')
            ->assertSee('Computer Basics');

        $this->actingAs($admin)
            ->put(route('admin.vocabulary.update', $term), [
                'subject_id' => $subject->id,
                'course_id' => $course->id,
                'term' => 'Computer Keyboard',
                'slug' => 'computer-keyboard',
                'meaning' => 'A keyboard used with a computer.',
                'example' => 'Press Enter on the computer keyboard.',
                'isl_video_url' => 'https://example.com/computer-keyboard.mp4',
                'sort_order' => 3,
            ])
            ->assertRedirect(route('admin.vocabulary.index'));

        $term->refresh();
        $this->assertSame('Computer Keyboard', $term->term);
        $this->assertSame('computer-keyboard', $term->slug);
        $this->assertFalse($term->is_published);

        $this->actingAs($admin)
            ->delete(route('admin.vocabulary.destroy', $term))
            ->assertRedirect(route('admin.vocabulary.index'));

        $this->assertDatabaseMissing('vocabulary_terms', ['id' => $term->id]);
    }

    public function test_admin_cannot_assign_a_non_isl_video_to_vocabulary_term(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $video = MediaAsset::create([
            'uploaded_by' => $admin->id,
            'title' => 'General Video',
            'media_type' => 'video',
            'is_isl' => false,
            'source' => 'external',
            'external_url' => 'https://example.com/general.mp4',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.vocabulary.create'))
            ->post(route('admin.vocabulary.store'), [
                'term' => 'Mouse',
                'isl_media_asset_id' => $video->id,
                'sort_order' => 0,
            ])
            ->assertRedirect(route('admin.vocabulary.create'))
            ->assertSessionHasErrors('isl_media_asset_id');

        $this->assertDatabaseCount('vocabulary_terms', 0);
    }

    public function test_learner_cannot_access_admin_vocabulary_management(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $response = $this->actingAs($learner)->get(route('admin.vocabulary.index'));

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403], true));
    }
}
