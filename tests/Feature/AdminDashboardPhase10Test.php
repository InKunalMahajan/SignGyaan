<?php

namespace Tests\Feature;

use App\Models\LearningClass;
use App\Models\User;
use App\Services\AdminDashboardData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardPhase10Test extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_real_platform_counts_and_operational_alerts(): void
    {
        $admin = User::factory()->create([
            'name' => 'Platform Admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $teacher = User::factory()->create([
            'name' => 'Teacher One',
            'role' => 'teacher',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Learner One',
            'role' => 'learner',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Inactive Parent',
            'role' => 'parents',
            'is_active' => false,
        ]);

        LearningClass::query()->create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC A',
            'code' => 'FYJC-A-2026',
            'subject' => 'Information Technology',
            'level' => 'FYJC',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);

        $data = app(AdminDashboardData::class)->get();

        $this->assertSame(4, $data['users']['total']);
        $this->assertSame(3, $data['users']['active']);
        $this->assertSame(1, $data['users']['inactive']);
        $this->assertSame(1, $data['users']['by_role']['teacher']['total']);
        $this->assertSame(1, $data['users']['by_role']['learner']['active']);
        $this->assertSame(1, $data['teaching']['active_classes']);
        $this->assertSame(1, $data['teaching']['classes_without_learners']);
        $this->assertSame(1, $data['teaching']['classes_without_courses']);

        $alertTitles = $data['alerts']->pluck('title');
        $this->assertTrue($alertTitles->contains('Active classes without learners'));
        $this->assertTrue($alertTitles->contains('Active classes without courses'));
        $this->assertTrue($alertTitles->contains('Inactive user accounts'));

        $this->actingAs($admin)
            ->get(route('dashboard.role', 'admin'))
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSee('Platform overview')
            ->assertSee('Users & roles')
            ->assertSee('Curriculum health')
            ->assertSee('Teaching operations')
            ->assertSee('Assessment & mastery')
            ->assertSee('Operational alerts')
            ->assertSee('Active classes without learners')
            ->assertSee('Active classes without courses');
    }

    public function test_admin_dashboard_has_usable_empty_state_and_non_admin_cannot_open_it(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard.role', 'admin'))
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSee('Manage SignGyaan')
            ->assertSee('Users & Roles')
            ->assertSee('Academic Structure')
            ->assertSee('Course Content')
            ->assertSee('Teaching Management')
            ->assertSee('Progress & Assessment');

        $this->actingAs($learner)
            ->get(route('dashboard.role', 'admin'))
            ->assertForbidden();
    }
}
