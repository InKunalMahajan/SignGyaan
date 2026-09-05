<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AssessmentExperiencePolishTest extends TestCase
{
    public function test_assessment_player_keeps_keyboard_and_screen_reader_landmarks(): void
    {
        $view = file_get_contents(resource_path('views/pages/assessments/player.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('data-assessment-player', $view);
        $this->assertStringContainsString('id="assessment-main-content"', $view);
        $this->assertStringContainsString('aria-current="page"', $view);
        $this->assertStringContainsString('aria-label="Question navigation"', $view);
        $this->assertStringContainsString(':aria-current="current ===', $view);
        $this->assertStringContainsString('role="progressbar"', $view);
        $this->assertStringContainsString('role="timer"', $view);
        $this->assertStringContainsString('aria-live="assertive"', $view);
        $this->assertStringContainsString('min-h-11', $view);
    }

    public function test_admin_results_table_has_accessible_scroll_and_table_semantics(): void
    {
        $view = file_get_contents(resource_path('views/admin/assessment-results/index.blade.php'));

        $this->assertIsString($view);
        $this->assertStringContainsString('data-admin-assessment-results', $view);
        $this->assertStringContainsString('aria-label="Filter assessment results"', $view);
        $this->assertStringContainsString('tabindex="0"', $view);
        $this->assertStringContainsString('<caption class="sr-only">', $view);
        $this->assertStringContainsString('scope="col"', $view);
    }

    public function test_global_styles_keep_accessibility_fallbacks_for_assessment_interfaces(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('@media (prefers-contrast: more)', $css);
        $this->assertStringContainsString('@media (forced-colors: active)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('[data-public-shell] input', $css);
        $this->assertStringContainsString('[data-admin-shell] .overflow-x-auto', $css);
    }

    public function test_complete_assessment_route_set_is_registered(): void
    {
        foreach ([
            'assessments.show',
            'assessments.start',
            'assessment-attempts.show',
            'assessment-attempts.save',
            'assessment-attempts.submit',
            'assessment-attempts.result',
            'admin.assessments.index',
            'admin.assessments.questions.index',
            'admin.assessment-results.index',
            'admin.assessment-results.show',
        ] as $routeName) {
            $this->assertTrue(Route::has($routeName), "Missing assessment route: {$routeName}");
        }
    }
}
