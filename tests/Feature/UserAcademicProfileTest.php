<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAcademicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_update_a_user_academic_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.academic-profile.edit', $learner))
            ->assertOk()
            ->assertSee('Academic Profile')
            ->assertSee('Maharashtra State Board')
            ->assertSee('CBSE')
            ->assertSee('NIOS');

        $this->actingAs($admin)
            ->patch(route('admin.users.academic-profile.update', $learner), [
                'education_board' => 'maharashtra',
                'standard' => '11',
                'academic_year' => now()->year.'-'.str_pad((string) ((now()->year + 1) % 100), 2, '0', STR_PAD_LEFT),
            ])
            ->assertRedirect(route('admin.users.academic-profile.edit', $learner));

        $learner->refresh();

        $this->assertSame('maharashtra', $learner->education_board);
        $this->assertSame('11', $learner->standard);
        $this->assertTrue($learner->hasAcademicProfile());
    }

    public function test_invalid_academic_profile_values_are_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->from(route('admin.users.academic-profile.edit', $learner))
            ->patch(route('admin.users.academic-profile.update', $learner), [
                'education_board' => 'unknown-board',
                'standard' => '99',
                'academic_year' => 'invalid-year',
            ])
            ->assertSessionHasErrors([
                'education_board',
                'standard',
                'academic_year',
            ]);
    }

    public function test_regular_admin_cannot_manage_super_administrator_academic_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.academic-profile.edit', $superAdmin))
            ->assertForbidden();
    }
}
