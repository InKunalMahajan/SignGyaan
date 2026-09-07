<?php

namespace Tests\Feature;

use App\Models\ParentLearnerLink;
use App\Models\ParentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParentLearnerLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_open_profile_and_default_profile_is_created(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);

        $response = $this->actingAs($parent)->get(route('parents.profile.show'));

        $response->assertOk();
        $this->assertDatabaseHas('parent_profiles', [
            'user_id' => $parent->id,
            'preferred_language' => 'english',
            'communication_mode' => 'both',
        ]);
    }

    public function test_non_parent_cannot_open_parent_profile(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('parents.profile.show'))
            ->assertForbidden();
    }

    public function test_parent_can_update_profile_preferences(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);

        $this->actingAs($parent)->put(route('parents.profile.update'), [
            'name' => 'Parent Example',
            'email' => 'parent@example.com',
            'phone' => '9999999999',
            'preferred_language' => 'marathi',
            'communication_mode' => 'isl',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $parent->id,
            'name' => 'Parent Example',
            'email' => 'parent@example.com',
        ]);

        $this->assertDatabaseHas('parent_profiles', [
            'user_id' => $parent->id,
            'phone' => '9999999999',
            'preferred_language' => 'marathi',
            'communication_mode' => 'isl',
        ]);
    }

    public function test_parent_can_send_link_request_to_active_learner_by_email(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create([
            'role' => 'learner',
            'email' => 'learner@example.com',
        ]);

        $this->actingAs($parent)->post(route('parents.learners.store'), [
            'learner_email' => 'learner@example.com',
            'relationship' => 'parent',
        ])->assertRedirect();

        $this->assertDatabaseHas('parent_learner_links', [
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'pending',
        ]);
    }

    public function test_parent_cannot_request_link_to_non_learner_account(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        User::factory()->create([
            'role' => 'teacher',
            'email' => 'teacher@example.com',
        ]);

        $this->actingAs($parent)->post(route('parents.learners.store'), [
            'learner_email' => 'teacher@example.com',
            'relationship' => 'guardian',
        ])->assertSessionHasErrors('learner_email');

        $this->assertDatabaseCount('parent_learner_links', 0);
    }

    public function test_duplicate_pending_request_is_rejected(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create([
            'role' => 'learner',
            'email' => 'learner@example.com',
        ]);

        ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'pending',
        ]);

        $this->actingAs($parent)->post(route('parents.learners.store'), [
            'learner_email' => 'learner@example.com',
            'relationship' => 'parent',
        ])->assertSessionHasErrors('learner_email');

        $this->assertDatabaseCount('parent_learner_links', 1);
    }

    public function test_learner_can_approve_own_parent_link_request(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create(['role' => 'learner']);

        $link = ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'guardian',
            'status' => 'pending',
        ]);

        $this->actingAs($learner)->patch(route('learner.parent-links.respond', $link), [
            'decision' => 'approve',
        ])->assertRedirect();

        $link->refresh();
        $this->assertSame('approved', $link->status);
        $this->assertNotNull($link->approved_at);
        $this->assertNotNull($link->responded_at);
    }

    public function test_another_learner_cannot_respond_to_someone_elses_request(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create(['role' => 'learner']);
        $otherLearner = User::factory()->create(['role' => 'learner']);

        $link = ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'pending',
        ]);

        $this->actingAs($otherLearner)->patch(route('learner.parent-links.respond', $link), [
            'decision' => 'approve',
        ])->assertForbidden();

        $this->assertDatabaseHas('parent_learner_links', [
            'id' => $link->id,
            'status' => 'pending',
        ]);
    }

    public function test_parent_can_only_view_approved_linked_learner(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create(['role' => 'learner']);

        $link = ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'pending',
        ]);

        $this->actingAs($parent)
            ->get(route('parents.learners.show', $link))
            ->assertForbidden();

        $link->update([
            'status' => 'approved',
            'approved_at' => now(),
            'responded_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get(route('parents.learners.show', $link))
            ->assertOk()
            ->assertSee($learner->name);
    }

    public function test_learner_can_revoke_approved_parent_access(): void
    {
        $parent = User::factory()->create(['role' => 'parents']);
        $learner = User::factory()->create(['role' => 'learner']);

        $link = ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'approved',
            'approved_at' => now(),
            'responded_at' => now(),
        ]);

        $this->actingAs($learner)
            ->delete(route('learner.parent-links.destroy', $link))
            ->assertRedirect();

        $this->assertDatabaseMissing('parent_learner_links', ['id' => $link->id]);
    }

    public function test_parent_password_change_requires_current_password(): void
    {
        $parent = User::factory()->create([
            'role' => 'parents',
            'password' => Hash::make('old-password'),
        ]);

        $this->actingAs($parent)->put(route('parents.profile.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($parent)->put(route('parents.profile.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('new-password-123', $parent->fresh()->password));
    }
}
