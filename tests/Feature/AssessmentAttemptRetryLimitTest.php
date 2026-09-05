<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentOption;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentAttemptRetryLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitted_attempt_can_be_followed_by_a_new_attempt_until_limit(): void
    {
        [$assessment, $question, $correct] = $this->createAssessment(2);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $first = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)->post(route('assessment-attempts.submit', [$assessment, $first]), [
            'answers' => [
                $question->id => ['option_ids' => [$correct->id]],
            ],
        ]);

        $this->actingAs($learner)
            ->post(route('assessments.start', $assessment))
            ->assertRedirect();

        $attempts = $assessment->attempts()
            ->where('user_id', $learner->id)
            ->orderBy('attempt_number')
            ->get();

        $this->assertCount(2, $attempts);
        $this->assertSame(1, $attempts[0]->attempt_number);
        $this->assertSame('submitted', $attempts[0]->status);
        $this->assertSame(2, $attempts[1]->attempt_number);
        $this->assertSame('in-progress', $attempts[1]->status);
    }

    public function test_existing_in_progress_attempt_is_reused_instead_of_creating_duplicate(): void
    {
        [$assessment] = $this->createAssessment(3);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->post(route('assessments.start', $assessment))
            ->assertRedirect(route('assessment-attempts.show', [$assessment, $attempt]));

        $this->assertSame(1, $assessment->attempts()->where('user_id', $learner->id)->count());
    }

    public function test_attempt_limit_counts_submitted_and_expired_attempts(): void
    {
        [$assessment] = $this->createAssessment(2);
        $learner = User::factory()->create(['role' => 'learner']);

        $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 1,
            'max_points' => 1,
            'percentage' => 100,
            'passed' => true,
            'started_at' => now()->subHours(2),
            'submitted_at' => now()->subHours(2)->addMinute(),
        ]);

        $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 2,
            'status' => 'expired',
            'started_at' => now()->subHour(),
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($learner)
            ->from(route('assessments.show', $assessment))
            ->post(route('assessments.start', $assessment))
            ->assertRedirect(route('assessments.show', $assessment))
            ->assertSessionHasErrors('assessment');

        $this->assertSame(2, $assessment->attempts()->where('user_id', $learner->id)->count());
    }

    public function test_overview_shows_attempt_history_best_score_and_remaining_attempts(): void
    {
        [$assessment] = $this->createAssessment(3);
        $learner = User::factory()->create(['role' => 'learner']);

        $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 0.5,
            'max_points' => 1,
            'percentage' => 50,
            'passed' => false,
            'started_at' => now()->subHours(2),
            'submitted_at' => now()->subHours(2)->addMinute(),
        ]);

        $best = $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 2,
            'status' => 'submitted',
            'score_points' => 1,
            'max_points' => 1,
            'percentage' => 100,
            'passed' => true,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subHour()->addMinute(),
        ]);

        $this->actingAs($learner)
            ->get(route('assessments.show', $assessment))
            ->assertOk()
            ->assertSee('Attempt history')
            ->assertSee('Best score:')
            ->assertSee('100.00%')
            ->assertSee('1 remaining')
            ->assertSee(route('assessment-attempts.result', [$assessment, $best]));
    }

    private function createAssessment(int $maxAttempts): array
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
            'title' => 'Input Devices Check',
            'slug' => 'input-devices-check',
            'kind' => 'practice',
            'resource_type' => 'quiz',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $assessment = Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 70,
            'max_attempts' => $maxAttempts,
            'show_feedback' => true,
            'is_published' => true,
        ]);

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which device is mainly used to type text?',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $correct = AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Keyboard',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Monitor',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        return [$assessment, $question, $correct];
    }
}
