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

class AssessmentAutomaticScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_validates_and_scores_all_supported_question_types(): void
    {
        [$assessment, $questions] = $this->createAssessmentWithQuestions();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $response = $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $questions['single']->id => [
                        'option_ids' => [$questions['singleCorrect']->id],
                    ],
                    $questions['multiple']->id => [
                        'option_ids' => [
                            $questions['multipleCorrectA']->id,
                            $questions['multipleCorrectB']->id,
                        ],
                    ],
                    $questions['trueFalse']->id => [
                        'option_ids' => [$questions['trueOption']->id],
                    ],
                    $questions['fill']->id => [
                        'text' => '   KEYBOARD   ',
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('assessment-attempts.result', [$assessment, $attempt]))
            ->assertSessionHas('status');

        $attempt->refresh();

        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('5.00', $attempt->score_points);
        $this->assertSame('5.00', $attempt->max_points);
        $this->assertSame('100.00', $attempt->percentage);
        $this->assertTrue($attempt->passed);
        $this->assertNotNull($attempt->submitted_at);

        $this->assertSame(4, $attempt->answers()->count());
        $this->assertSame(4, $attempt->answers()->where('is_correct', true)->count());

        $fillAnswer = $attempt->answers()
            ->where('assessment_question_id', $questions['fill']->id)
            ->firstOrFail();

        $this->assertSame(['text' => 'KEYBOARD'], $fillAnswer->response);
        $this->assertTrue($fillAnswer->is_correct);
        $this->assertSame('1.00', $fillAnswer->points_awarded);
    }

    public function test_multiple_choice_requires_exact_correct_set_and_does_not_award_partial_credit(): void
    {
        [$assessment, $questions] = $this->createAssessmentWithQuestions();
        $assessment->update(['passing_percentage' => 50]);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $questions['single']->id => [
                        'option_ids' => [$questions['singleCorrect']->id],
                    ],
                    $questions['multiple']->id => [
                        'option_ids' => [$questions['multipleCorrectA']->id],
                    ],
                    $questions['trueFalse']->id => [
                        'option_ids' => [$questions['falseOption']->id],
                    ],
                    $questions['fill']->id => [
                        'text' => 'mouse',
                    ],
                ],
            ])
            ->assertRedirect(route('assessment-attempts.result', [$assessment, $attempt]));

        $attempt->refresh();

        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('1.00', $attempt->score_points);
        $this->assertSame('5.00', $attempt->max_points);
        $this->assertSame('20.00', $attempt->percentage);
        $this->assertFalse($attempt->passed);

        $multipleAnswer = $attempt->answers()
            ->where('assessment_question_id', $questions['multiple']->id)
            ->firstOrFail();

        $this->assertFalse($multipleAnswer->is_correct);
        $this->assertSame('0.00', $multipleAnswer->points_awarded);
    }

    public function test_required_questions_must_be_answered_before_submission(): void
    {
        [$assessment, $questions] = $this->createAssessmentWithQuestions();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->from(route('assessment-attempts.show', [$assessment, $attempt]))
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $questions['single']->id => [
                        'option_ids' => [$questions['singleCorrect']->id],
                    ],
                ],
            ])
            ->assertRedirect(route('assessment-attempts.show', [$assessment, $attempt]))
            ->assertSessionHasErrors([
                'answers.'.$questions['multiple']->id,
                'answers.'.$questions['trueFalse']->id,
                'answers.'.$questions['fill']->id,
            ]);

        $attempt->refresh();
        $this->assertSame('in-progress', $attempt->status);
        $this->assertNull($attempt->submitted_at);
        $this->assertSame(0, $attempt->answers()->count());
    }

    public function test_submission_rejects_option_ids_from_another_question(): void
    {
        [$assessment, $questions] = $this->createAssessmentWithQuestions();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->from(route('assessment-attempts.show', [$assessment, $attempt]))
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $questions['single']->id => [
                        'option_ids' => [$questions['multipleCorrectA']->id],
                    ],
                    $questions['multiple']->id => [
                        'option_ids' => [
                            $questions['multipleCorrectA']->id,
                            $questions['multipleCorrectB']->id,
                        ],
                    ],
                    $questions['trueFalse']->id => [
                        'option_ids' => [$questions['trueOption']->id],
                    ],
                    $questions['fill']->id => [
                        'text' => 'keyboard',
                    ],
                ],
            ])
            ->assertRedirect(route('assessment-attempts.show', [$assessment, $attempt]))
            ->assertSessionHasErrors('answers.'.$questions['single']->id);

        $this->assertSame('in-progress', $attempt->fresh()->status);
    }

    public function test_expired_attempt_is_not_scored(): void
    {
        [$assessment, $questions] = $this->createAssessmentWithQuestions();
        $learner = User::factory()->create(['role' => 'learner']);

        $attempt = $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'in-progress',
            'started_at' => now()->subHour(),
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $questions['single']->id => [
                        'option_ids' => [$questions['singleCorrect']->id],
                    ],
                ],
            ])
            ->assertRedirect(route('assessments.show', $assessment))
            ->assertSessionHasErrors('assessment');

        $attempt->refresh();
        $this->assertSame('expired', $attempt->status);
        $this->assertNull($attempt->submitted_at);
        $this->assertSame('0.00', $attempt->score_points);
    }

    private function createAssessmentWithQuestions(): array
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

        $single = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which device is mainly used to type text?',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $singleCorrect = AssessmentOption::create([
            'assessment_question_id' => $single->id,
            'option_text' => 'Keyboard',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        AssessmentOption::create([
            'assessment_question_id' => $single->id,
            'option_text' => 'Monitor',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $multiple = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'multiple-choice',
            'prompt' => 'Which two are input devices?',
            'points' => 2,
            'sort_order' => 2,
            'is_required' => true,
            'is_published' => true,
        ]);

        $multipleCorrectA = AssessmentOption::create([
            'assessment_question_id' => $multiple->id,
            'option_text' => 'Keyboard',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $multipleCorrectB = AssessmentOption::create([
            'assessment_question_id' => $multiple->id,
            'option_text' => 'Mouse',
            'is_correct' => true,
            'sort_order' => 2,
        ]);

        AssessmentOption::create([
            'assessment_question_id' => $multiple->id,
            'option_text' => 'Printer',
            'is_correct' => false,
            'sort_order' => 3,
        ]);

        $trueFalse = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'true-false',
            'prompt' => 'A keyboard is an input device.',
            'points' => 1,
            'sort_order' => 3,
            'is_required' => true,
            'is_published' => true,
        ]);

        $trueOption = AssessmentOption::create([
            'assessment_question_id' => $trueFalse->id,
            'option_text' => 'True',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $falseOption = AssessmentOption::create([
            'assessment_question_id' => $trueFalse->id,
            'option_text' => 'False',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        $fill = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'A device used for typing is called a _____.',
            'answer_key' => ['accepted_answers' => ['keyboard']],
            'points' => 1,
            'sort_order' => 4,
            'is_required' => true,
            'is_published' => true,
        ]);

        return [$assessment, [
            'single' => $single,
            'singleCorrect' => $singleCorrect,
            'multiple' => $multiple,
            'multipleCorrectA' => $multipleCorrectA,
            'multipleCorrectB' => $multipleCorrectB,
            'trueFalse' => $trueFalse,
            'trueOption' => $trueOption,
            'falseOption' => $falseOption,
            'fill' => $fill,
        ]];
    }
}
