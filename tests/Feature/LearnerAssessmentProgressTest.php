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

class LearnerAssessmentProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_current_assessment_progress_and_scores(): void
    {
        [$assessment] = $this->createPublishedAssessment();
        $learner = User::factory()->create(['role' => 'learner']);

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 8,
            'max_points' => 10,
            'percentage' => 80,
            'passed' => true,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(50),
        ]);

        $activeAttempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
            'attempt_number' => 2,
            'status' => 'in-progress',
            'started_at' => now()->subMinutes(5),
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Assessment progress')
            ->assertSee('Hardware Check')
            ->assertSee('Continue Assessment')
            ->assertSee('80.00%')
            ->assertSee(route('assessment-attempts.show', [$assessment, $activeAttempt]), false);
    }

    public function test_my_learning_shows_attempt_history_and_result_links(): void
    {
        [$assessment] = $this->createPublishedAssessment();
        $learner = User::factory()->create(['role' => 'learner']);

        $submittedAttempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 6,
            'max_points' => 10,
            'percentage' => 60,
            'passed' => false,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(45),
        ]);

        $this->actingAs($learner)
            ->get(route('my-learning'))
            ->assertOk()
            ->assertSee('Assessment progress')
            ->assertSee('View Result')
            ->assertSee('60.00%')
            ->assertSee(route('assessment-attempts.result', [$assessment, $submittedAttempt]), false);
    }

    public function test_attempts_from_unpublished_assessments_are_not_exposed_in_learning_pages(): void
    {
        [$assessment] = $this->createPublishedAssessment();
        $learner = User::factory()->create(['role' => 'learner']);

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 10,
            'max_points' => 10,
            'percentage' => 100,
            'passed' => true,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
        ]);

        $assessment->update(['is_published' => false]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No assessment attempts yet')
            ->assertDontSee('Hardware Check');

        $this->actingAs($learner)
            ->get(route('my-learning'))
            ->assertOk()
            ->assertSee('No assessment attempts yet')
            ->assertDontSee('Hardware Check');
    }

    private function createPublishedAssessment(): array
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
            'is_featured' => true,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Hardware',
            'slug' => 'hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Input Devices',
            'slug' => 'input-devices',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $practice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Hardware Check',
            'slug' => 'hardware-check',
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

        return [$assessment, $lesson];
    }
}
