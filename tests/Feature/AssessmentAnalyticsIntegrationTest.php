<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use App\Services\AssessmentAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentAnalyticsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_summary_calculates_program_metrics(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Summary');
        $assessment = $this->assessment($teacher, $course, 'Analytics Test', 20, 8);
        $learnerA = User::factory()->create(['role' => 'learner']);
        $learnerB = User::factory()->create(['role' => 'learner']);
        $learnerC = User::factory()->create(['role' => 'learner']);

        $this->attempt($assessment, $learnerA, 'completed', 16, 80, true);
        $this->attempt($assessment, $learnerB, 'completed', 8, 40, false);
        $this->attempt($assessment, $learnerC, 'pending_review', 10, null, null);

        $summary = app(AssessmentAnalyticsService::class)->assessmentSummary($assessment);

        $this->assertSame(3, $summary['appeared']);
        $this->assertSame(2, $summary['completed']);
        $this->assertSame(1, $summary['pending_review']);
        $this->assertSame(1, $summary['passed']);
        $this->assertSame(1, $summary['failed']);
        $this->assertSame(50.0, $summary['pass_percentage']);
        $this->assertSame(60.0, $summary['average_percentage']);
        $this->assertSame(12.0, $summary['average_marks']);
        $this->assertSame(80.0, $summary['highest_percentage']);
        $this->assertSame(40.0, $summary['lowest_percentage']);
    }

    public function test_question_analysis_reports_accuracy_and_pending_review(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Questions');
        $assessment = $this->assessment($teacher, $course, 'Question Analysis', 4, 2);
        $objective = $assessment->questions()->create([
            'type' => 'single_choice',
            'prompt' => 'Choose A',
            'marks' => 2,
            'position' => 1,
            'config' => ['options' => ['A', 'B']],
            'answer_key' => ['value' => 'A'],
        ]);
        $short = $assessment->questions()->create([
            'type' => 'short_answer',
            'prompt' => 'Explain IT',
            'marks' => 2,
            'position' => 2,
            'config' => [],
            'answer_key' => null,
        ]);
        $learnerA = User::factory()->create(['role' => 'learner']);
        $learnerB = User::factory()->create(['role' => 'learner']);
        $attemptA = $this->attempt($assessment, $learnerA, 'completed', 4, 100, true);
        $attemptB = $this->attempt($assessment, $learnerB, 'pending_review', 0, null, null);

        AssessmentAnswer::create([
            'attempt_id' => $attemptA->id,
            'question_id' => $objective->id,
            'response' => ['value' => 'A'],
            'question_snapshot' => ['position' => 1, 'marks' => '2.00'],
            'awarded_marks' => 2,
            'is_correct' => true,
            'requires_review' => false,
        ]);
        AssessmentAnswer::create([
            'attempt_id' => $attemptB->id,
            'question_id' => $objective->id,
            'response' => ['value' => 'B'],
            'question_snapshot' => ['position' => 1, 'marks' => '2.00'],
            'awarded_marks' => 0,
            'is_correct' => false,
            'requires_review' => false,
        ]);
        AssessmentAnswer::create([
            'attempt_id' => $attemptA->id,
            'question_id' => $short->id,
            'response' => ['value' => 'Good answer'],
            'question_snapshot' => ['position' => 2, 'marks' => '2.00'],
            'awarded_marks' => 2,
            'is_correct' => true,
            'requires_review' => false,
        ]);
        AssessmentAnswer::create([
            'attempt_id' => $attemptB->id,
            'question_id' => $short->id,
            'response' => ['value' => 'Waiting'],
            'question_snapshot' => ['position' => 2, 'marks' => '2.00'],
            'awarded_marks' => null,
            'is_correct' => null,
            'requires_review' => true,
        ]);

        $analysis = app(AssessmentAnalyticsService::class)->questionAnalysis($assessment)->keyBy('question_id');

        $this->assertSame(2, $analysis[$objective->id]['responses']);
        $this->assertSame(1, $analysis[$objective->id]['correct']);
        $this->assertSame(1, $analysis[$objective->id]['incorrect']);
        $this->assertSame(50.0, $analysis[$objective->id]['accuracy_percentage']);
        $this->assertSame(1.0, $analysis[$objective->id]['average_marks']);

        $this->assertSame(2, $analysis[$short->id]['responses']);
        $this->assertSame(1, $analysis[$short->id]['pending_review']);
        $this->assertSame(100.0, $analysis[$short->id]['accuracy_percentage']);
    }

    public function test_mastery_signals_include_only_completed_assessment_attempts(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Mastery');
        $assessment = $this->assessment($teacher, $course, 'Mastery Input', 10, 4);
        $learner = User::factory()->create(['role' => 'learner']);
        $completed = $this->attempt($assessment, $learner, 'completed', 8, 80, true);
        $this->attempt($assessment, $learner, 'pending_review', 4, null, null, 2);

        $signals = app(AssessmentAnalyticsService::class)->masterySignalsForLearner($learner);

        $this->assertCount(1, $signals);
        $this->assertSame('assessment', $signals->first()['source']);
        $this->assertSame($course->id, $signals->first()['course_id']);
        $this->assertSame($assessment->id, $signals->first()['assessment_id']);
        $this->assertSame($completed->id, $signals->first()['attempt_id']);
        $this->assertSame(80.0, $signals->first()['percentage']);
        $this->assertTrue($signals->first()['passed']);
    }

    public function test_teacher_analytics_and_learner_history_are_role_owned(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Ownership');
        [$otherTeacher] = $this->teacherWithCourse('Other');
        $assessment = $this->assessment($teacher, $course, 'Owned Analytics', 10, 4);
        $learner = User::factory()->create(['role' => 'learner']);
        $otherLearner = User::factory()->create(['role' => 'learner']);
        $attempt = $this->attempt($assessment, $learner, 'completed', 8, 80, true);
        $this->attempt($assessment, $otherLearner, 'completed', 5, 50, true);

        $this->actingAs($teacher)
            ->get(route('teacher.assessments.analytics', $assessment))
            ->assertOk()
            ->assertSee('Owned Analytics')
            ->assertSee('Question-wise Analysis');

        $this->actingAs($otherTeacher)
            ->get(route('teacher.assessments.analytics', $assessment))
            ->assertForbidden();

        $this->actingAs($learner)
            ->get(route('learner.assessments.history'))
            ->assertOk()
            ->assertSee('Owned Analytics')
            ->assertSee('80.00%')
            ->assertDontSee((string) $otherLearner->name);

        $this->assertSame($learner->id, $attempt->learner_id);
    }

    private function teacherWithCourse(string $suffix): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => "IT {$suffix}",
            'slug' => 'it-'.strtolower($suffix).'-'.uniqid(),
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => "Course {$suffix}",
            'slug' => 'course-'.strtolower($suffix).'-'.uniqid(),
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        return [$teacher, $course];
    }

    private function assessment(User $teacher, Course $course, string $title, float $total, float $pass): Assessment
    {
        return Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => $title,
            'status' => 'published',
            'total_marks' => $total,
            'passing_marks' => $pass,
            'published_at' => now(),
        ]);
    }

    private function attempt(
        Assessment $assessment,
        User $learner,
        string $status,
        float $marks,
        ?float $percentage,
        ?bool $passed,
        int $number = 1
    ): AssessmentAttempt {
        return AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => $number,
            'status' => $status,
            'earned_marks' => $marks,
            'total_marks_snapshot' => $assessment->total_marks,
            'passing_marks_snapshot' => $assessment->passing_marks,
            'percentage' => $percentage,
            'passed' => $passed,
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(10),
            'reviewed_at' => $status === 'completed' ? now() : null,
        ]);
    }
}
