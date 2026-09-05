<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssessmentResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_assessment_performance_and_attempt_details(): void
    {
        [$assessment, $attempt, $learner] = $this->createSubmittedAttempt();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.assessment-results.index'))
            ->assertOk()
            ->assertSee('Assessment Results')
            ->assertSee($learner->name)
            ->assertSee('80.00%');

        $this->actingAs($admin)
            ->get(route('admin.assessment-results.show', $attempt))
            ->assertOk()
            ->assertSee($learner->name)
            ->assertSee('Input Devices Check')
            ->assertSee('Which device is used for typing?');
    }

    public function test_non_admin_cannot_view_admin_assessment_results(): void
    {
        [, $attempt] = $this->createSubmittedAttempt();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('admin.assessment-results.index'))
            ->assertForbidden();

        $this->actingAs($learner)
            ->get(route('admin.assessment-results.show', $attempt))
            ->assertForbidden();
    }

    private function createSubmittedAttempt(): array
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
            'max_attempts' => 3,
            'show_feedback' => true,
            'is_published' => true,
        ]);

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'Which device is used for typing?',
            'answer_key' => ['accepted_answers' => ['keyboard']],
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $learner = User::factory()->create(['role' => 'learner']);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 0.8,
            'max_points' => 1,
            'percentage' => 80,
            'passed' => true,
            'started_at' => now()->subMinutes(10),
            'submitted_at' => now(),
        ]);

        $attempt->answers()->create([
            'assessment_question_id' => $question->id,
            'response' => ['text' => 'keyboard'],
            'question_snapshot' => $question->prompt,
            'is_correct' => true,
            'points_awarded' => 0.8,
            'answered_at' => now(),
        ]);

        return [$assessment, $attempt, $learner];
    }
}
