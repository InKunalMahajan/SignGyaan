<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibleIslVideoPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_isl_video_renders_with_native_controls_for_accessible_enhancement(): void
    {
        [$subject, $course, $unit] = $this->createPublishedCourseTree();

        $asset = MediaAsset::create([
            'title' => 'Hardware in ISL',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/hardware.mp4',
            'mime_type' => 'video/mp4',
            'language_code' => 'is',
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Hardware Basics',
            'slug' => 'hardware-basics',
            'isl_media_asset_id' => $asset->id,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]))
            ->assertOk()
            ->assertSee('<video controls preload="metadata"', false)
            ->assertSee('aria-label="ISL video for Hardware Basics"', false)
            ->assertSee('https://example.com/hardware.mp4', false);
    }

    public function test_player_script_keeps_native_controls_and_adds_keyboard_playback_tools(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('enhanceAccessibleIslVideos', $script);
        $this->assertStringContainsString('#lesson-video video', $script);
        $this->assertStringContainsString("video.controls = true", $script);
        $this->assertStringContainsString("aria-keyshortcuts", $script);
        $this->assertStringContainsString("Space K J L ArrowLeft ArrowRight M F", $script);
        $this->assertStringContainsString("Playback speed", $script);
        $this->assertStringContainsString("Rewind 10 seconds", $script);
        $this->assertStringContainsString("Forward 10 seconds", $script);
        $this->assertStringContainsString("Turn captions on", $script);
        $this->assertStringContainsString("requestFullscreen", $script);
        $this->assertStringContainsString("Keyboard shortcuts", $script);
        $this->assertStringContainsString("data.accessiblePlayerReady", str_replace('dataset', 'data', $script));
    }

    public function test_non_direct_video_still_uses_safe_external_open_link(): void
    {
        [$subject, $course, $unit] = $this->createPublishedCourseTree();

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'External ISL Lesson',
            'slug' => 'external-isl-lesson',
            'isl_video_url' => 'https://video.example.com/watch/isl-lesson',
            'isl_video_title' => 'External ISL Lesson Video',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]))
            ->assertOk()
            ->assertSee('Open ISL Video')
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('<video controls preload="metadata"', false);
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

        return [$subject, $course, $unit];
    }
}
