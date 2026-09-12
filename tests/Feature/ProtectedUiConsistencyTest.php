<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProtectedUiConsistencyTest extends TestCase
{
    public function test_shared_protected_styles_enforce_visible_form_controls_and_workspace_fit(): void
    {
        $css = file_get_contents(resource_path('css/protected-actions.css'));
        $appJs = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("import '../css/protected-actions.css';", $appJs);

        $this->assertStringContainsString("#main-content input:not([type='hidden'])", $css);
        $this->assertStringContainsString('#main-content select', $css);
        $this->assertStringContainsString('#main-content textarea', $css);
        $this->assertStringContainsString('border: 1px solid #9ca3af !important;', $css);
        $this->assertStringContainsString('border-color: #111111 !important;', $css);
        $this->assertStringContainsString('min-height: 42px !important;', $css);

        $this->assertStringContainsString('#main-content {', $css);
        $this->assertStringContainsString('overflow-x: clip;', $css);
        $this->assertStringContainsString('#main-content .overflow-x-auto', $css);
        $this->assertStringContainsString('.mx-auto.max-w-3xl:has(form)', $css);
        $this->assertStringContainsString('max-width: 64rem !important;', $css);
    }
}
