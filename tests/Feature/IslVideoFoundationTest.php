<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IslVideoFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_isl_video_metadata_in_media_library(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'title' => 'Input Devices in ISL',
                'media_type' => 'video',
                'source' => 'external',
                'external_url' => 'https://example.com/input-devices.mp4',
                'caption' => 'ISL explanation of common input devices.',
                'is_isl' => '1',
                'language_code' => 'is',
                'duration_seconds' => 185,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.media.index'));

        $asset = MediaAsset::query()->firstOrFail();

        $this->assertTrue($asset->is_isl);
        $this->assertSame('is', $asset->language_code);
        $this->assertSame(185, $asset->duration_seconds);
        $this->assertSame('3:05', $asset->formattedDuration());
    }

    public function test_admin_can_store_lesson_fallback_video_metadata(): void
    {
        [$unit] = $this->createPublishedCourseTree();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.lessons.store'), [
                'unit_id' => $unit->id,
                'title' => 'Input Devices',
                'slug' => 'input-devices',
                'short_description' => 'Learn common input devices.',
                'isl_video_url' => 'https://example.com/fallback-input-devices.mp4',
                'isl_video_title' => 'Input Devices — ISL Explanation',
                'isl_video_caption' => 'Fallback ISL video for this lesson.',
                'sort_order' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.lessons.index'));

        $lesson = Lesson::query()->firstOrFail();

        $this->assertSame('Input Devices — ISL Explanation', $lesson->isl_video_title);
        $this->assertSame('Fallback ISL video for this lesson.', $lesson->isl_video_caption);
    }

    public function test_published_media_video_is_used_publicly_and_draft_media_falls_back_to_external_url(): void
    {
        [$unit, $subject, $course] = $this->createPublishedCourseTree();

        $asset = MediaAsset::create([
            'title' => 'Published ISL Hardware Video',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/published-hardware.mp4',
            'caption' => 'Published Media Library ISL caption.',
            'language_code' => 'is',
            'duration_seconds' => 120,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Hardware Basics',
            'slug' => 'hardware-basics',
            'isl_video_url' => 'https://example.com/fallback-hardware.mp4',
            'isl_video_title' => 'Fallback Hardware ISL',
            'isl_video_caption' => 'Fallback caption.',
            'isl_media_asset_id' => $asset->id,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $url = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('https://example.com/published-hardware.mp4', false)
            ->assertSee('Published Media Library ISL caption.')
            ->assertDontSee('https://example.com/fallback-hardware.mp4', false);

        $asset->update(['is_published' => false]);

        $this->get($url)
            ->assertOk()
            ->assertSee('https://example.com/fallback-hardware.mp4', false)
            ->assertDontSee('Published Media Library ISL caption.');
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
            'title' => 'Hardware',
            'slug' => 'hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$unit, $subject, $course];
    }
}
