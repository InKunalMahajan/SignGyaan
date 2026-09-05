<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Services\LearnerDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContinueLearningDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_resumes_the_saved_lesson_with_context(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $unit, $lessonOne, $lessonTwo] = $this->publishedCourse('digital-skills', 'computer-basics');

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonTwo->id,
            'completed_lessons' => ['lesson-'.$lessonOne->id],
            'last_accessed_at' => now(),
        ]);

        $resumeUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lessonTwo->id,
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Continue learning')
            ->assertSee('Resume lesson')
            ->assertSee($course->title)
            ->assertSee($unit->title)
            ->assertSee($lessonTwo->title)
            ->assertSee('1 of 2 lessons completed')
            ->assertSee('50%')
            ->assertSee($resumeUrl, false);
    }

    public function test_continue_learning_orders_active_courses_by_recent_activity_and_excludes_completed_courses(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        [$subjectA, $courseA, , $aOne, $aTwo] = $this->publishedCourse('digital-skills', 'computer-basics');
        [$subjectB, $courseB, , $bOne, $bTwo] = $this->publishedCourse('office-skills', 'office-basics');
        [$subjectC, $courseC, , $cOne, $cTwo] = $this->publishedCourse('web-skills', 'web-basics');

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectA->slug,
            'subject_name' => $subjectA->name,
            'course_slug' => $courseA->slug,
            'course_title' => $courseA->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$aTwo->id,
            'completed_lessons' => ['lesson-'.$aOne->id],
            'last_accessed_at' => now()->subDays(2),
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectB->slug,
            'subject_name' => $subjectB->name,
            'course_slug' => $courseB->slug,
            'course_title' => $courseB->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$bTwo->id,
            'completed_lessons' => ['lesson-'.$bOne->id],
            'last_accessed_at' => now(),
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectC->slug,
            'subject_name' => $subjectC->name,
            'course_slug' => $courseC->slug,
            'course_title' => $courseC->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$cTwo->id,
            'completed_lessons' => ['lesson-'.$cOne->id, 'lesson-'.$cTwo->id],
            'last_accessed_at' => now()->subDay(),
            'completed_at' => now()->subDay(),
        ]);

        $dashboard = app(LearnerDashboardService::class)->build($learner);

        $this->assertSame(
            [$courseB->title, $courseA->title],
            $dashboard['continueLearning']->pluck('course_title')->all()
        );
        $this->assertSame($courseB->title, $dashboard['primaryContinueLearning']['course_title']);
        $this->assertNotContains($courseC->title, $dashboard['continueLearning']->pluck('course_title')->all());

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder([$courseB->title, $courseA->title])
            ->assertSee('Courses you finished')
            ->assertSee($courseC->title);
    }

    private function publishedCourse(string $subjectSlug, string $courseSlug): array
    {
        $subject = Subject::create([
            'name' => str($subjectSlug)->replace('-', ' ')->title()->toString(),
            'slug' => $subjectSlug,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => str($courseSlug)->replace('-', ' ')->title()->toString(),
            'slug' => $courseSlug,
            'level' => 'Beginner',
            'short_description' => 'Published learner course.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonOne = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Introduction lesson.',
            'estimated_duration_minutes' => 8,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonTwo = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Next Lesson',
            'slug' => 'next-lesson',
            'content' => 'Continue learning here.',
            'estimated_duration_minutes' => 12,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lessonOne, $lessonTwo];
    }
}
