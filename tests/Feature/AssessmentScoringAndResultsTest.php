<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use App\Services\AssessmentScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentScoringAndResultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_server_scores_all_objective_question_types_and_completes_result(): void
    {
        [$teacher, $learner, $assessment] = $this->assessmentFixture(10, 6);

        $questions = collect([
            $this->question($assessment, 'single_choice', 2, ['options' => ['A', 'B']], ['value' => 'B'], 1),
            $this->question($assessment, 'multi_select', 2, ['options' => ['A', 'B', 'C']], ['values' => ['A', 'C']], 2),
            $this->question($assessment, 'true_false', 2, [], ['value' => true], 3),
            $this->question($assessment, 'fill_blank', 2, [], ['accepted' => ['Central Processing Unit', 'CPU']], 4),
            $this->question($assessment, 'matching', 2, ['pairs' => [
                ['left' => 'CPU', 'right' => 'Processor'],
                ['left' => 'RAM', 'right' => 'Memory'],
            ]], ['pairs' => [
                ['left' => 'CPU', 'right' => 'Processor'],
                ['left' => 'RAM', 'right' => 'Memory'],
            ]], 5),
        ]);

        $attempt = $this->submittedAttempt($assessment, $learner);
        $responses = [
            ['value' => 'B'],
            ['values' => ['C', 'A']],
            ['value' => 'True'],
            ['value' => '  cpu  '],
            ['pairs' => ['CPU' => 'Processor', 'RAM' => 'Memory']],
        ];

        foreach ($questions as $index => $question) {
            AssessmentAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'response' => $responses[$index],
                'question_snapshot' => $this->snapshot($question),
                'requires_review' => false,
            ]);
        }

        $result = app(AssessmentScoringService::class)->scoreAttempt($attempt);

        $this->assertSame('completed', $result->status);
        $this->assertSame('10.00', $result->earned_marks);
        $this->assertSame('100.00', $result->percentage);
        $this->assertTrue($result->passed);
        $this->assertTrue($result->answers->every(fn ($answer) => $answer->is_correct === true));
        $this->assertTrue($result->answers->every(fn ($answer) => $answer->requires_review === false));
    }

    public function test_short_answer_waits_for_teacher_review_then_recalculates_pass_fail(): void
    {
        [$teacher, $learner, $assessment] = $this->assessmentFixture(5, 4);
        $objective = $this->question($assessment, 'true_false', 2, [], ['value' => true], 1);
        $manual = $this->question($assessment, 'short_answer', 3, [], null, 2);
        $attempt = $this->submittedAttempt($assessment, $learner, 'pending_review');

        AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $objective->id,
            'response' => ['value' => 'True'],
            'question_snapshot' => $this->snapshot($objective),
            'requires_review' => false,
        ]);
        $manualAnswer = AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $manual->id,
            'response' => ['value' => 'A computer processes data into information.'],
            'question_snapshot' => $this->snapshot($manual),
            'requires_review' => true,
        ]);

        $pending = app(AssessmentScoringService::class)->scoreAttempt($attempt);
        $this->assertSame('pending_review', $pending->status);
        $this->assertSame('2.00', $pending->earned_marks);

        $this->actingAs($teacher)
            ->patch(route('teacher.assessments.attempts.update', $attempt), [
                'scores' => [$manualAnswer->id => 2.5],
                'feedback' => [$manualAnswer->id => 'Good explanation.'],
            ])
            ->assertRedirect();

        $attempt->refresh();
        $manualAnswer->refresh();

        $this->assertSame('completed', $attempt->status);
        $this->assertSame('4.50', $attempt->earned_marks);
        $this->assertSame('90.00', $attempt->percentage);
        $this->assertTrue($attempt->passed);
        $this->assertFalse($manualAnswer->requires_review);
        $this->assertSame('2.50', $manualAnswer->awarded_marks);
        $this->assertSame('Good explanation.', $manualAnswer->feedback);
    }

    public function test_teacher_cannot_award_more_than_question_marks_or_review_another_teachers_attempt(): void
    {
        [$teacher, $learner, $assessment] = $this->assessmentFixture(3, 2);
        $manual = $this->question($assessment, 'short_answer', 3, [], null, 1);
        $attempt = $this->submittedAttempt($assessment, $learner, 'pending_review');
        $answer = AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $manual->id,
            'response' => ['value' => 'Answer'],
            'question_snapshot' => $this->snapshot($manual),
            'requires_review' => true,
        ]);

        $this->actingAs($teacher)
            ->from(route('teacher.assessments.attempts.review', $attempt))
            ->patch(route('teacher.assessments.attempts.update', $attempt), [
                'scores' => [$answer->id => 4],
            ])
            ->assertSessionHasErrors("scores.{$answer->id}");

        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($otherTeacher)
            ->get(route('teacher.assessments.attempts.review', $attempt))
            ->assertForbidden();
    }

    public function test_learner_can_view_only_their_own_submitted_result(): void
    {
        [, $learner, $assessment] = $this->assessmentFixture(2, 1);
        $question = $this->question($assessment, 'single_choice', 2, ['options' => ['A', 'B']], ['value' => 'A'], 1);
        $attempt = $this->submittedAttempt($assessment, $learner);
        AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'response' => ['value' => 'A'],
            'question_snapshot' => $this->snapshot($question),
            'requires_review' => false,
        ]);

        $this->actingAs($learner)
            ->get(route('learner.assessments.attempts.result', $attempt))
            ->assertOk()
            ->assertSee('Assessment Result');

        $otherLearner = User::factory()->create(['role' => 'learner']);
        $this->actingAs($otherLearner)
            ->get(route('learner.assessments.attempts.result', $attempt))
            ->assertForbidden();
    }

    private function assessmentFixture(float $totalMarks, ?float $passingMarks): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Assessment Scoring '.uniqid(),
            'slug' => 'assessment-scoring-'.uniqid(),
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Scoring Course '.uniqid(),
            'slug' => 'scoring-course-'.uniqid(),
            'level' => 'Beginner',
            'is_active' => true,
        ]);
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Scoring Test',
            'status' => 'published',
            'total_marks' => $totalMarks,
            'passing_marks' => $passingMarks,
            'published_at' => now(),
        ]);

        return [$teacher, $learner, $assessment];
    }

    private function question(
        Assessment $assessment,
        string $type,
        float $marks,
        array $config,
        ?array $answerKey,
        int $position
    ): AssessmentQuestion {
        return AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => $type,
            'prompt' => "Question {$position}",
            'marks' => $marks,
            'position' => $position,
            'config' => $config,
            'answer_key' => $answerKey,
        ]);
    }

    private function submittedAttempt(
        Assessment $assessment,
        User $learner,
        string $status = 'submitted'
    ): AssessmentAttempt {
        return AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => $status,
            'total_marks_snapshot' => $assessment->total_marks,
            'passing_marks_snapshot' => $assessment->passing_marks,
            'started_at' => now()->subMinutes(10),
            'submitted_at' => now(),
        ]);
    }

    private function snapshot(AssessmentQuestion $question): array
    {
        return [
            'type' => $question->type,
            'prompt' => $question->prompt,
            'marks' => (string) $question->marks,
            'position' => $question->position,
            'config' => $question->config,
            'answer_key' => $question->answer_key,
            'explanation' => $question->explanation,
        ];
    }
}
