<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProtectedHeaderConsistencyTest extends TestCase
{
    public function test_shared_protected_layouts_use_the_account_menu_component(): void
    {
        $layouts = [
            'resources/views/admin/layout.blade.php' => 'role="admin"',
            'resources/views/teacher/layout.blade.php' => 'role="teacher"',
            'resources/views/learner/layout.blade.php' => 'role="learner"',
        ];

        foreach ($layouts as $path => $roleAttribute) {
            $contents = file_get_contents(base_path($path));

            $this->assertStringContainsString('<x-app-account-menu', $contents, $path);
            $this->assertStringContainsString($roleAttribute, $contents, $path);
        }
    }

    public function test_shared_account_menu_contains_profile_settings_and_sign_out(): void
    {
        $contents = file_get_contents(base_path('resources/views/components/app-account-menu.blade.php'));

        $this->assertStringContainsString('Profile', $contents);
        $this->assertStringContainsString('Settings', $contents);
        $this->assertStringContainsString('Sign out', $contents);
        $this->assertStringContainsString("route('logout')", $contents);
    }

    public function test_all_standalone_protected_pages_expose_a_logout_source_for_the_fallback_header(): void
    {
        $views = [
            'resources/views/dashboard.blade.php',
            'resources/views/learner/classes/show.blade.php',
            'resources/views/learner/classes/course.blade.php',
            'resources/views/learner/classes/lesson.blade.php',
            'resources/views/learner/profile.blade.php',
            'resources/views/parents/profile.blade.php',
            'resources/views/parents/learner.blade.php',
        ];

        foreach ($views as $path) {
            $contents = file_get_contents(base_path($path));

            $this->assertStringContainsString("route('logout')", $contents, $path);
        }
    }

    public function test_protected_shell_enforces_full_width_header_and_72px_rhythm(): void
    {
        $contents = file_get_contents(base_path('resources/css/protected-app-shell.css'));

        $this->assertStringContainsString('min-height: 72px', $contents);
        $this->assertStringContainsString('max-width: none !important', $contents);
        $this->assertStringContainsString('margin-left: auto', $contents);
    }

    public function test_fallback_header_never_recenters_or_caps_protected_pages(): void
    {
        $contents = file_get_contents(base_path('resources/css/app-header-fallback.css'));

        $this->assertStringContainsString('max-width: none', $contents);
        $this->assertStringContainsString('margin: 0', $contents);
        $this->assertStringContainsString('padding-left: 40px', $contents);
        $this->assertStringContainsString('padding-right: 40px', $contents);
        $this->assertStringNotContainsString('max-width: 80rem', $contents);
    }
}
