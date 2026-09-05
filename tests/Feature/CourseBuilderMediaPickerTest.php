<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBuilderMediaPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_media_picker_and_filter_isl_video(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $islVideo = MediaAsset::query()->create([
            'uploaded_by' => $admin->id,
            'title' => 'Keyboard ISL Sign',
            'media_type' => 'video',
            'is_isl' => true,
            'source' => 'external',
            'external_url' => 'https://example.com/keyboard.mp4',
            'duration_seconds' => 42,
            'is_published' => true,
        ]);

        MediaAsset::query()->create([
            'uploaded_by' => $admin->id,
            'title' => 'Keyboard Diagram',
            'media_type' => 'image',
            'is_isl' => false,
            'source' => 'external',
            'external_url' => 'https://example.com/keyboard.png',
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.media.picker', ['q' => 'Keyboard', 'type' => 'video', 'isl' => 1]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $islVideo->id)
            ->assertJsonPath('data.0.is_isl', true)
            ->assertJsonPath('data.0.media_type', 'video');
    }

    public function test_media_picker_requires_admin_access(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->getJson(route('admin.media.picker'))
            ->assertForbidden();
    }

    public function test_admin_layout_loads_media_picker_enhancement(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $script = file_get_contents(public_path('js/admin-media-picker.js'));

        $this->assertStringContainsString('admin-media-picker.js', $layout);
        $this->assertStringContainsString('select[name="media_asset_id"]', $script);
        $this->assertStringContainsString('data-media-search', $script);
        $this->assertStringContainsString('data-media-type', $script);
        $this->assertStringContainsString('data-media-isl', $script);
        $this->assertStringContainsString('Use this media', $script);
        $this->assertStringContainsString('dialog.showModal()', $script);
    }
}
