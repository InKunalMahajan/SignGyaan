<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAuthoringFinalAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_builder_keeps_admin_navigation_and_keyboard_ordering_accessible(): void
    {
        [$admin, $course] = $this->courseFixture();

        $response = $this->actingAs($admin)->get(route('admin.courses.builder', $course));

        $response
            ->assertOk()
            ->assertSee('Skip to admin content')
            ->assertSee('id="admin-main-content" tabindex="-1"', false)
            ->assertSee('data-builder-save-status', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('data-drag-handle', false)
            ->assertSee('data-move="up"', false)
            ->assertSee('data-move="down"', false)
            ->assertSee('js/admin-authoring-accessibility.js', false);
    }

    public function test_publishing_page_exposes_semantic_progress_and_bulk_action_help(): void
    {
        [$admin, $course] = $this->courseFixture();

        $response = $this->actingAs($admin)->get(route('admin.courses.publishing-checklist', $course));

        $response
            ->assertOk()
            ->assertSee('role="progressbar"', false)
            ->assertSee('aria-valuemin="0"', false)
            ->assertSee('aria-valuemax="100"', false)
            ->assertSee('aria-valuenow=', false)
            ->assertSee('id="publish-all-help"', false)
            ->assertSee('aria-describedby="publish-all-help"', false)
            ->assertSee('id="draft-all-help"', false)
            ->assertSee('aria-describedby="draft-all-help"', false)
            ->assertSee('sm:flex-none', false);
    }

    public function test_final_admin_authoring_script_supports_coarse_pointer_reordering_focus_and_scrollable_tables(): void
    {
        $script = file_get_contents(public_path('js/admin-authoring-accessibility.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString("matchMedia('(pointer: fine)')", $script);
        $this->assertStringContainsString("item.draggable = finePointer", $script);
        $this->assertStringContainsString("aria-describedby", $script);
        $this->assertStringContainsString("focus-visible:ring-4", $script);
        $this->assertStringContainsString("overflow-x-auto", $script);
        $this->assertStringContainsString("opens in a new tab", $script);
    }

    public function test_restored_application_accessibility_shell_has_no_noop_regression(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertIsString($script);
        $this->assertStringContainsString('const enhancePublicShell = () => {', $script);
        $this->assertStringContainsString('const enhanceAdminShell = () => {', $script);
        $this->assertStringContainsString('const trapAdminDrawerFocus = (event) => {', $script);
        $this->assertStringNotContainsString('const enhancePublicShell = () => {};', $script);
        $this->assertStringNotContainsString('const enhanceAdminShell = () => {};', $script);
        $this->assertStringNotContainsString('const trapAdminDrawerFocus = () => false;', $script);
    }

    public function test_learner_cannot_access_final_admin_authoring_pages(): void
    {
        [, $course] = $this->courseFixture();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('admin.courses.builder', $course))
            ->assertForbidden();

        $this->actingAs($learner)
            ->get(route('admin.courses.publishing-checklist', $course))
            ->assertForbidden();

        $this->actingAs($learner)
            ->get(route('admin.courses.preview', $course))
            ->assertForbidden();
    }

    private function courseFixture(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
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
            'short_description' => 'Learn the foundations of using a computer.',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => 'A computer is an electronic device used to process information.',
            'estimated_duration_minutes' => 10,
            'sort_order' => 1,
            'is_published' => false,
        ]);

        return [$admin, $course, $unit];
    }
}
