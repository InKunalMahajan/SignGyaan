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

class AssessmentLearnerFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_redirects_to_owned_result_page_with_score_and_feedback(): void
    {
        [$assessment, $question, $correctOption] = $this->createAssessment(showFeedback: true);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $response = $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $question->id => ['option_ids' => [$correctOption->id]],
                ],
            ]);

        $response->assertRedirect(route('assessment-attempts.result', [$assessment, $attempt]));

        $this->actingAs($learner)
            ->get(route('assessment-attempts.result', [$assessment, $attempt]))
            ->assertOk()
            ->assertSee('100.00%')
            ->assertSee('Passed')
            ->assertSee('Review your answers')
            ->assertSee('Keyboard')
            ->assertSee('A keyboard is the main text-entry device.')
            ->assertSee('Good choice.');
    }

    public function test_feedback_disabled_hides_correct_answers_explanations_and_option_feedback(): void
    {
        [$assessment, $question, $correctOption] = $this->createAssessment(showFeedback: false);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $question->id => ['option_ids' => [$correctOption->id]],
                ],
            ]);

        $this->actingAs($learner)
            ->get(route('assessment-attempts.result', [$assessment, $attempt]))
            ->assertOk()
            ->assertSee('100.00%')
            ->assertSee('Detailed feedback is not available')
            ->assertDontSee('A keyboard is the main text-entry device.')
            ->assertDontSee('Good choice.');
    }

    public function test_learner_cannot_view_another_learners_result(): void
    {
        [$assessment, $question, $correctOption] = $this->createAssessment(showFeedback: true);
        $owner = User::factory()->create(['role' => 'learner']);
        $other = User::factory()->create(['role' => 'learner']);

        $this->actingAs($owner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $owner->id)->firstOrFail();

        $this->actingAs($owner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $question->id => ['option_ids' => [$correctOption->id]],
                ],
            ]);

        $this->actingAs($other)
            ->get(route('assessment-attempts.result', [$assessment, $attempt]))
            ->assertNotFound();
    }

    public function test_optional_unanswered_question_is_preserved_in_submitted_review_snapshot(): void
    {
        [$assessment, $question, $correctOption] = $this->createAssessment(showFeedback: true);

        $optional = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'Optional typing question',
            'answer_key' => ['accepted_answers' => ['mouse']],
            'points' => 1,
            'sort_order' => 2,
            'is_required' => false,
            'is_published' => true,
        ]);

        $learner = User::factory()->create(['role' => 'learner']);
        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
                'answers' => [
                    $question->id => ['option_ids' => [$correctOption->id]],
                ],
            ])
            ->assertRedirect(route('assessment-attempts.result', [$assessment, $attempt]));

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_attempt_id' => $attempt->id,
            'assessment_question_id' => $optional->id,
            'is_correct' => false,
            'points_awarded' => 0,
        ]);

        $this->actingAs($learner)
            ->get(route('assessment-attempts.result', [$assessment, $attempt]))
            ->assertOk()
            ->assertSee('Optional typing question')
            ->assertSee('Answered')
            ->assertSee('1 / 2');
    }

    private function createAssessment(bool $showFeedback): array
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
            'show_feedback' => $showFeedback,
            'is_published' => true,
        ]);

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which device is mainly used to type text?',
            'explanation' => 'A keyboard is the main text-entry device.',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $correctOption = AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Keyboard',
            'feedback' => 'Good choice.',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Monitor',
            'feedback' => 'A monitor displays output.',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        return [$assessment, $question, $correctOption];
    }
}
