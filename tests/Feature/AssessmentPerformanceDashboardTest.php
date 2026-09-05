<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentPerformanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_sees_assessment_performance_summary_and_grouped_results(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$assessment, $practice] = $this->publishedAssessment();

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 6,
            'max_points' => 10,
            'percentage' => 60,
            'passed' => false,
            'started_at' => now()->subDays(2),
            'submitted_at' => now()->subDays(2),
        ]);

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => 2,
            'status' => 'submitted',
            'score_points' => 9,
            'max_points' => 10,
            'percentage' => 90,
            'passed' => true,
            'started_at' => now()->subDay(),
            'submitted_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->get(route('assessment-performance'))
            ->assertOk()
            ->assertSee('Assessment Performance')
            ->assertSee($practice->title)
            ->assertSee('90.00%')
            ->assertSee('75.00%')
            ->assertSee('50%')
            ->assertSee('2 attempts')
            ->assertSee('View Latest Result');
    }

    public function test_in_progress_attempt_is_exposed_as_continue_action(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        [$assessment, $practice] = $this->publishedAssessment();

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => 1,
            'status' => 'in-progress',
            'started_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('assessment-performance'))
            ->assertOk()
            ->assertSee($practice->title)
            ->assertSee('In progress')
            ->assertSee('Continue Attempt');
    }

    public function test_guest_cannot_open_assessment_performance_page(): void
    {
        $this->get(route('assessment-performance'))
            ->assertRedirect(route('login'));
    }

    private function publishedAssessment(): array
    {
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

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Computer Introduction',
            'slug' => 'computer-introduction',
            'content' => 'Lesson content.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $practice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Computer Basics Quiz',
            'slug' => 'computer-basics-quiz',
            'kind' => 'practice',
            'resource_type' => 'quiz',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $assessment = Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 70,
            'max_attempts' => 3,
            'show_feedback' => true,
            'is_published' => true,
        ]);

        return [$assessment, $practice];
    }
}
