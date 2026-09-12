<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssessmentFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_tables_have_required_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('assessments', [
            'course_id', 'created_by', 'title', 'status', 'total_marks', 'passing_marks', 'published_at',
        ]));
        $this->assertTrue(Schema::hasColumns('assessment_questions', [
            'assessment_id', 'type', 'prompt', 'marks', 'position', 'config', 'answer_key',
        ]));
        $this->assertTrue(Schema::hasColumns('assessment_attempts', [
            'assessment_id', 'learner_id', 'attempt_number', 'status', 'earned_marks',
            'total_marks_snapshot', 'passing_marks_snapshot', 'percentage', 'passed', 'submitted_at',
        ]));
        $this->assertTrue(Schema::hasColumns('assessment_answers', [
            'attempt_id', 'question_id', 'response', 'question_snapshot', 'awarded_marks',
            'is_correct', 'requires_review', 'feedback',
        ]));
    }

    public function test_models_persist_relationships_json_and_status_helpers(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology-assessment-test',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Assessment Basics',
            'slug' => 'assessment-basics',
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Unit Test 1',
            'status' => 'draft',
            'total_marks' => 20,
            'passing_marks' => 8,
        ]);
        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => 'single_choice',
            'prompt' => 'Which option is correct?',
            'marks' => 2,
            'position' => 1,
            'config' => ['options' => ['A', 'B', 'C']],
            'answer_key' => ['value' => 'B'],
        ]);
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'in_progress',
            'total_marks_snapshot' => 20,
            'passing_marks_snapshot' => 8,
            'started_at' => now(),
        ]);
        $answer = AssessmentAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'response' => ['value' => 'B'],
            'question_snapshot' => ['prompt' => $question->prompt, 'marks' => '2.00'],
            'requires_review' => false,
        ]);

        $this->assertTrue($course->assessments()->whereKey($assessment->id)->exists());
        $this->assertTrue($teacher->createdAssessments()->whereKey($assessment->id)->exists());
        $this->assertTrue($learner->assessmentAttempts()->whereKey($attempt->id)->exists());
        $this->assertSame(['options' => ['A', 'B', 'C']], $question->fresh()->config);
        $this->assertSame(['value' => 'B'], $answer->fresh()->response);
        $this->assertFalse($assessment->isPublished());
        $this->assertFalse($attempt->isSubmitted());
        $this->assertFalse($question->requiresManualReview());

        $question->update(['type' => 'short_answer']);
        $attempt->update(['status' => 'submitted']);

        $this->assertTrue($question->fresh()->requiresManualReview());
        $this->assertTrue($attempt->fresh()->isSubmitted());
        $this->assertContains('matching', AssessmentQuestion::TYPES);
    }
}
