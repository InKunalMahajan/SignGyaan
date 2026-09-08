<?php

namespace Tests\Feature;

use App\Models\LearnerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LearnerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_learner_profile(): void
    {
        $this->get('/learner/profile')
            ->assertRedirect('/login');
    }

    public function test_non_learner_cannot_open_learner_profile(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->get('/learner/profile')
            ->assertForbidden();
    }

    public function test_learner_can_open_profile_and_defaults_are_created(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get('/learner/profile')
            ->assertOk()
            ->assertSee('Learner Profile & Settings');

        $this->assertDatabaseHas('learner_profiles', [
            'user_id' => $learner->id,
            'preferred_language' => 'english',
            'communication_mode' => 'both',
            'captions_enabled' => true,
            'text_size' => 'standard',
        ]);
    }

    public function test_learner_can_update_profile_and_accessibility_preferences(): void
    {
        $learner = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->put('/learner/profile', [
                'name' => 'New Learner Name',
                'email' => 'learner@example.com',
                'education_level' => 'Junior College',
                'class_grade' => 'FYJC',
                'institution' => 'SignGyaan Learning Centre',
                'preferred_language' => 'marathi',
                'communication_mode' => 'isl',
                'captions_enabled' => '1',
                'high_contrast' => '1',
                'reduced_motion' => '1',
                'text_size' => 'large',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $learner->id,
            'name' => 'New Learner Name',
            'email' => 'learner@example.com',
        ]);

        $this->assertDatabaseHas('learner_profiles', [
            'user_id' => $learner->id,
            'education_level' => 'Junior College',
            'class_grade' => 'FYJC',
            'preferred_language' => 'marathi',
            'communication_mode' => 'isl',
            'captions_enabled' => true,
            'high_contrast' => true,
            'reduced_motion' => true,
            'text_size' => 'large',
        ]);
    }

    public function test_learner_can_change_password_with_current_password(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
            'password' => 'old-password',
        ]);

        $this->actingAs($learner)
            ->put('/learner/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-password-123', $learner->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
            'password' => 'old-password',
        ]);

        $this->actingAs($learner)
            ->from('/learner/profile')
            ->put('/learner/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect('/learner/profile')
            ->assertSessionHasErrors('current_password');
    }

    public function test_learner_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');

        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2nPAAAAAASUVORK5CYII=');
        $avatar = UploadedFile::fake()->createWithContent('avatar.png', $png);

        $this->actingAs($learner)
            ->post('/learner/profile/avatar', ['avatar' => $avatar])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $profile = LearnerProfile::where('user_id', $learner->id)->firstOrFail();
        $this->assertNotNull($profile->avatar_path);
        Storage::disk('public')->assertExists($profile->avatar_path);

        $storedPath = $profile->avatar_path;

        $this->actingAs($learner)
            ->delete('/learner/profile/avatar')
            ->assertSessionHas('status');

        Storage::disk('public')->assertMissing($storedPath);
        $this->assertNull($profile->fresh()->avatar_path);
    }
}
