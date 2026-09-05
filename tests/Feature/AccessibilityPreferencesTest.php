<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibilityPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_accessibility_preferences(): void
    {
        $this->get(route('profile.accessibility'))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_save_accessibility_preferences(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->patch(route('profile.accessibility.update'), [
                'captions' => 'prefer',
                'transcript' => 'hide',
                'simple_summary' => 'hide',
                'reduced_motion' => 'on',
            ])
            ->assertRedirect();

        $user->refresh();

        $this->assertSame([
            'captions' => 'prefer',
            'transcript' => 'hide',
            'simple_summary' => 'hide',
            'reduced_motion' => 'on',
        ], $user->accessibility_preferences);
    }

    public function test_saved_preferences_are_exposed_to_public_layout(): void
    {
        $user = User::factory()->create([
            'role' => 'learner',
            'accessibility_preferences' => [
                'captions' => 'prefer',
                'transcript' => 'hide',
                'simple_summary' => 'show',
                'reduced_motion' => 'on',
            ],
        ]);

        $this->actingAs($user)
            ->get(route('profile.accessibility'))
            ->assertOk()
            ->assertSee('Accessibility Preferences')
            ->assertSee('data-prefer-captions="prefer"', false)
            ->assertSee('data-prefer-transcript="hide"', false)
            ->assertSee('data-prefer-simple-summary="show"', false)
            ->assertSee('data-reduced-motion="on"', false);
    }

    public function test_invalid_accessibility_preference_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'learner']);

        $this->actingAs($user)
            ->from(route('profile.accessibility'))
            ->patch(route('profile.accessibility.update'), [
                'captions' => 'always-force',
                'transcript' => 'show',
                'simple_summary' => 'show',
                'reduced_motion' => 'system',
            ])
            ->assertRedirect(route('profile.accessibility'))
            ->assertSessionHasErrors('captions', 'accessibility');
    }

    public function test_accessibility_css_and_caption_preference_support_exist(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('data-prefer-transcript="hide"', $css);
        $this->assertStringContainsString('data-prefer-simple-summary="hide"', $css);
        $this->assertStringContainsString('data-reduced-motion="on"', $css);
        $this->assertStringContainsString("dataset.preferCaptions === 'prefer'", $layout);
        $this->assertStringContainsString("['captions', 'subtitles']", $layout);
    }
}
