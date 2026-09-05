<?php

namespace Tests\Feature;

use Tests\TestCase;

class Task10FinalAccessibilityRegressionTest extends TestCase
{
    public function test_public_layout_keeps_core_accessibility_landmarks_and_preferences(): void
    {
        $layout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('href="#main-content"', $layout);
        $this->assertStringContainsString('id="main-content"', $layout);
        $this->assertStringContainsString('tabindex="-1"', $layout);
        $this->assertStringContainsString('data-prefer-captions=', $layout);
        $this->assertStringContainsString('data-prefer-transcript=', $layout);
        $this->assertStringContainsString('data-prefer-simple-summary=', $layout);
        $this->assertStringContainsString('data-reduced-motion=', $layout);
        $this->assertStringContainsString('meta name="csrf-token"', $layout);
    }

    public function test_javascript_keeps_shell_keyboard_accessibility_and_video_progress_together(): void
    {
        $javascript = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('const enhancePublicShell = () => {', $javascript);
        $this->assertStringContainsString('const enhanceAdminShell = () => {', $javascript);
        $this->assertStringContainsString('const trapAdminDrawerFocus = (event) => {', $javascript);
        $this->assertStringContainsString('const returnFocusFromHeaderSearch = (input) => {', $javascript);
        $this->assertStringContainsString('const enhanceAccessibleIslVideos = () => {', $javascript);
        $this->assertStringContainsString('const enhanceLessonVideoProgress = () => {', $javascript);
        $this->assertStringContainsString("video.setAttribute('aria-keyshortcuts', 'Space K J L ArrowLeft ArrowRight M F')", $javascript);
        $this->assertStringContainsString("'X-CSRF-TOKEN': csrf", $javascript);
        $this->assertStringContainsString('Lesson completion remains manual.', $javascript);

        $this->assertStringNotContainsString('const enhancePublicShell = () => {};', $javascript);
        $this->assertStringNotContainsString('const enhanceAdminShell = () => {};', $javascript);
        $this->assertStringNotContainsString('const trapAdminDrawerFocus = () => false;', $javascript);
    }

    public function test_lesson_view_keeps_responsive_text_video_and_vocabulary_support(): void
    {
        $lesson = file_get_contents(resource_path('views/partials/course/lesson.blade.php'));
        $linkedVocabulary = file_get_contents(resource_path('views/partials/course/linked-vocabulary.blade.php'));
        $progress = file_get_contents(resource_path('views/partials/course/progress-actions.blade.php'));

        $this->assertStringContainsString('id="lesson-video"', $lesson);
        $this->assertStringContainsString('id="lesson-summary"', $lesson);
        $this->assertStringContainsString('id="lesson-transcript"', $lesson);
        $this->assertStringContainsString('id="lesson-vocabulary"', $lesson);
        $this->assertStringContainsString('aspect-video w-full', $lesson);
        $this->assertStringContainsString('sm:rounded-3xl', $lesson);
        $this->assertStringContainsString('print:hidden', $lesson);

        $this->assertStringContainsString('id="lesson-isl-vocabulary"', $linkedVocabulary);
        $this->assertStringContainsString('sm:grid-cols-2', $linkedVocabulary);
        $this->assertStringContainsString('lg:grid-cols-3', $linkedVocabulary);

        $this->assertStringContainsString('data-video-progress-config', $progress);
        $this->assertStringContainsString('Complete & Continue', $progress);
        $this->assertStringContainsString('never marks the lesson complete automatically', $progress);
    }

    public function test_styles_cover_mobile_reduced_motion_contrast_and_forced_colors(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('@media (max-width: 639px)', $css);
        $this->assertStringContainsString('@media (min-width: 640px) and (max-width: 1023px)', $css);
        $this->assertStringContainsString('@media (prefers-contrast: more)', $css);
        $this->assertStringContainsString('@media (forced-colors: active)', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('[data-public-shell][data-reduced-motion="on"]', $css);
        $this->assertStringContainsString('[data-public-shell][data-prefer-transcript="hide"] #lesson-transcript', $css);
        $this->assertStringContainsString('[data-public-shell][data-prefer-simple-summary="hide"] #lesson-summary', $css);
    }

    public function test_video_progress_and_accessibility_preferences_endpoints_remain_authenticated(): void
    {
        $routes = file_get_contents(base_path('routes/auth.php'));

        $this->assertStringContainsString("Route::middleware('auth')->group", $routes);
        $this->assertStringContainsString("'/learning-progress/video'", $routes);
        $this->assertStringContainsString("->name('learning-progress.video.store')", $routes);
        $this->assertStringContainsString("'/profile/accessibility'", $routes);
        $this->assertStringContainsString("->name('profile.accessibility.update')", $routes);
    }
}
