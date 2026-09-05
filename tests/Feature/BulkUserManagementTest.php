<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BulkUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_bulk_user_management(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.bulk.index'))
            ->assertOk()
            ->assertSee('Bulk User Management')
            ->assertSee('Import CSV')
            ->assertSee('Download Users CSV');
    }

    public function test_super_admin_can_import_a_new_learner_from_csv(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $csv = implode("\n", [
            'name,email,role,status,password,education_board,standard,academic_year',
            'Anaya Student,anaya@example.com,learner,active,Password123,cbse,8,2026-27',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.bulk.import'), [
            'csv_file' => UploadedFile::fake()->createWithContent('users.csv', $csv),
        ]);

        $response->assertRedirect(route('admin.users.bulk.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'anaya@example.com',
            'name' => 'Anaya Student',
            'role' => User::ROLE_LEARNER,
            'status' => User::STATUS_ACTIVE,
            'education_board' => 'cbse',
            'standard' => '8',
            'academic_year' => '2026-27',
        ]);
    }

    public function test_bulk_status_action_updates_selected_users_but_not_signed_in_admin(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $learnerOne = User::factory()->learner()->create();
        $learnerTwo = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.bulk.action'), [
                'user_ids' => [$admin->id, $learnerOne->id, $learnerTwo->id],
                'bulk_action' => 'status',
                'status' => User::STATUS_SUSPENDED,
            ])
            ->assertRedirect(route('admin.users.bulk.index'));

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status' => User::STATUS_ACTIVE]);
        $this->assertDatabaseHas('users', ['id' => $learnerOne->id, 'status' => User::STATUS_SUSPENDED]);
        $this->assertDatabaseHas('users', ['id' => $learnerTwo->id, 'status' => User::STATUS_SUSPENDED]);
    }

    public function test_regular_admin_cannot_change_roles_in_bulk(): void
    {
        $admin = User::factory()->admin()->create();
        $learner = User::factory()->learner()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.bulk.action'), [
                'user_ids' => [$learner->id],
                'bulk_action' => 'role',
                'role' => User::ROLE_TEACHER,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $learner->id, 'role' => User::ROLE_LEARNER]);
    }

    public function test_export_excludes_passwords_and_contains_account_fields(): void
    {
        $admin = User::factory()->superAdmin()->create();
        User::factory()->learner()->create(['email' => 'learner@example.com']);

        $response = $this->actingAs($admin)->get(route('admin.users.bulk.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        $this->assertStringContainsString('name,email,role,status,education_board,standard,academic_year', $content);
        $this->assertStringContainsString('learner@example.com', $content);
        $this->assertStringNotContainsString('password', strtolower($content));
    }
}
