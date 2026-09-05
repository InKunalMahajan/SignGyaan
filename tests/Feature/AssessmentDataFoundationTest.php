<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
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

class AssessmentDataFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_interactive_assessment_relations_store_questions_attempts_and_answers(): void
    {
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Computer Foundations',
            'slug' => 'computer-foundations',
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Hardware Basics',
            'slug' => 'hardware-basics',
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

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which part is used to type text?',
            'points' => 1,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $keyboard = AssessmentOption::create([
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

        $user = User::factory()->create();

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $user->id,
            'attempt_number' => 1,
            'status' => 'submitted',
            'score_points' => 1,
            'max_points' => 1,
            'percentage' => 100,
            'passed' => true,
            'started_at' => now()->subMinute(),
            'submitted_at' => now(),
        ]);

        AssessmentAnswer::create([
            'assessment_attempt_id' => $attempt->id,
            'assessment_question_id' => $question->id,
            'response' => ['option_ids' => [$keyboard->id]],
            'question_snapshot' => $question->prompt,
            'is_correct' => true,
            'points_awarded' => 1,
            'answered_at' => now(),
        ]);

        $this->assertTrue($practice->fresh()->assessment->is($assessment));
        $this->assertTrue($assessment->fresh()->practiceResource->is($practice));
        $this->assertCount(1, $assessment->fresh()->questions);
        $this->assertCount(2, $question->fresh()->options);
        $this->assertCount(1, $assessment->fresh()->attempts);
        $this->assertCount(1, $attempt->fresh()->answers);
        $this->assertCount(1, $user->fresh()->assessmentAttempts);
        $this->assertTrue($attempt->fresh()->isSubmitted());
        $this->assertSame(['option_ids' => [$keyboard->id]], $attempt->fresh()->answers->first()->response);
    }
}
