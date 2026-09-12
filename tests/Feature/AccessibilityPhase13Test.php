<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccessibilityPhase13Test extends TestCase
{
    public function test_shared_accessibility_runtime_is_loaded_by_the_application(): void
    {
        $bootstrap = file_get_contents(resource_path('js/bootstrap.js'));

        $this->assertStringContainsString("import './accessibility';", $bootstrap);
        $this->assertStringContainsString("import '../css/accessibility.css';", $bootstrap);
    }

    public function test_runtime_provides_keyboard_semantic_form_table_and_media_support(): void
    {
        $js = file_get_contents(resource_path('js/accessibility.js'));

        $this->assertStringContainsString('Skip to main content', $js);
        $this->assertStringContainsString("main.setAttribute('role', 'main')", $js);
        $this->assertStringContainsString("aria-current", $js);
        $this->assertStringContainsString("aria-required", $js);
        $this->assertStringContainsString("aria-invalid", $js);
        $this->assertStringContainsString("aria-describedby", $js);
        $this->assertStringContainsString("aria-live", $js);
        $this->assertStringContainsString("scope', 'col'", $js);
        $this->assertStringContainsString("track.kind = 'captions'", $js);
        $this->assertStringContainsString('data-a11y-setting="largeText"', $js);
        $this->assertStringContainsString('data-a11y-setting="highContrast"', $js);
        $this->assertStringContainsString('data-a11y-setting="reduceMotion"', $js);
        $this->assertStringContainsString("event.key === 'Escape'", $js);
    }

    public function test_accessibility_styles_cover_focus_reflow_contrast_motion_and_forced_colors(): void
    {
        $css = file_get_contents(resource_path('css/accessibility.css'));

        $this->assertStringContainsString('.sg-skip-link', $css);
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('min-height: 44px;', $css);
        $this->assertStringContainsString("[aria-invalid='true']", $css);
        $this->assertStringContainsString('.sg-a11y-large-text', $css);
        $this->assertStringContainsString('.sg-a11y-high-contrast', $css);
        $this->assertStringContainsString('.sg-a11y-reduce-motion', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('@media (prefers-contrast: more)', $css);
        $this->assertStringContainsString('@media (forced-colors: active)', $css);
        $this->assertStringContainsString('max-width: 100%;', $css);
    }

    public function test_accessibility_documentation_preserves_deaf_and_isl_boundaries(): void
    {
        $documentation = file_get_contents(base_path('docs/accessibility.md'));

        $this->assertStringContainsString('Deaf / hard-of-hearing media support', $documentation);
        $this->assertStringContainsString('Do not treat plain English text as an Indian Sign Language translation.', $documentation);
        $this->assertStringContainsString('captions', $documentation);
        $this->assertStringContainsString('transcript', $documentation);
        $this->assertStringContainsString('manual keyboard and screen-reader spot check', $documentation);
    }
}
