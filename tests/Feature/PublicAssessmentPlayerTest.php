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

class PublicAssessmentPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_assessment_intro_is_public_but_draft_assessment_is_not(): void
    {
        [$assessment] = $this->createAssessment();

        $this->get(route('assessments.show', $assessment))
            ->assertOk()
            ->assertSee('Hardware Check')
            ->assertSee('Sign in to start');

        $assessment->update(['is_published' => false]);

        $this->get(route('assessments.show', $assessment))
            ->assertNotFound();
    }

    public function test_guest_must_sign_in_to_start_assessment(): void
    {
        [$assessment] = $this->createAssessment();

        $this->post(route('assessments.start', $assessment))
            ->assertRedirect(route('login'));
    }

    public function test_learner_can_start_and_open_an_in_progress_attempt(): void
    {
        [$assessment, $question] = $this->createAssessment(timeLimit: 15);
        $learner = User::factory()->create(['role' => 'learner']);

        $response = $this->actingAs($learner)
            ->post(route('assessments.start', $assessment));

        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $response->assertRedirect(route('assessment-attempts.show', [$assessment, $attempt]));
        $this->assertSame('in-progress', $attempt->status);
        $this->assertNotNull($attempt->started_at);
        $this->assertNotNull($attempt->expires_at);

        $this->actingAs($learner)
            ->get(route('assessment-attempts.show', [$assessment, $attempt]))
            ->assertOk()
            ->assertSee($question->prompt)
            ->assertSee('Save Answers')
            ->assertDontSee('SECRET_EXPLANATION')
            ->assertDontSee('SECRET_OPTION_FEEDBACK');
    }

    public function test_learner_can_save_draft_answers_without_scoring_them(): void
    {
        [$assessment, $question, $correctOption] = $this->createAssessment();
        $fillQuestion = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'A device used for typing is called a _____.',
            'answer_key' => ['accepted_answers' => ['SECRET_TYPED_ANSWER']],
            'points' => 1,
            'sort_order' => 2,
            'is_required' => true,
            'is_published' => true,
        ]);

        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)
            ->post(route('assessment-attempts.save', [$assessment, $attempt]), [
                'answers' => [
                    $question->id => ['option_ids' => [$correctOption->id]],
                    $fillQuestion->id => ['text' => 'keyboard'],
                ],
            ])
            ->assertRedirect(route('assessment-attempts.show', [$assessment, $attempt]));

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_attempt_id' => $attempt->id,
            'assessment_question_id' => $question->id,
            'is_correct' => null,
            'points_awarded' => 0,
        ]);

        $saved = $attempt->answers()->where('assessment_question_id', $fillQuestion->id)->firstOrFail();
        $this->assertSame(['text' => 'keyboard'], $saved->response);
        $this->assertNull($saved->is_correct);
        $this->assertSame('0.00', $saved->points_awarded);

        $this->actingAs($learner)
            ->get(route('assessment-attempts.show', [$assessment, $attempt]))
            ->assertOk()
            ->assertSee('keyboard')
            ->assertDontSee('SECRET_TYPED_ANSWER');
    }

    public function test_learner_cannot_open_another_users_attempt(): void
    {
        [$assessment] = $this->createAssessment();
        $owner = User::factory()->create(['role' => 'learner']);
        $otherLearner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($owner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $owner->id)->firstOrFail();

        $this->actingAs($otherLearner)
            ->get(route('assessment-attempts.show', [$assessment, $attempt]))
            ->assertNotFound();
    }

    public function test_attempt_limit_is_enforced(): void
    {
        [$assessment] = $this->createAssessment(maxAttempts: 1);
        $learner = User::factory()->create(['role' => 'learner']);

        $assessment->attempts()->create([
            'user_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'expired',
            'started_at' => now()->subHour(),
            'expires_at' => now()->subMinute(),
        ]);

        $this->actingAs($learner)
            ->from(route('assessments.show', $assessment))
            ->post(route('assessments.start', $assessment))
            ->assertRedirect(route('assessments.show', $assessment))
            ->assertSessionHasErrors('assessment');

        $this->assertSame(1, $assessment->attempts()->where('user_id', $learner->id)->count());
    }

    private function createAssessment(?int $timeLimit = null, ?int $maxAttempts = 3): array
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
            'short_description' => 'Check your understanding of common input devices.',
            'instructions' => 'Read each question carefully and choose the best answer.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $assessment = Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 70,
            'max_attempts' => $maxAttempts,
            'time_limit_minutes' => $timeLimit,
            'show_feedback' => true,
            'is_published' => true,
        ]);

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'single-choice',
            'prompt' => 'Which device is mainly used to type text?',
            'explanation' => 'SECRET_EXPLANATION',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $correctOption = AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Keyboard',
            'feedback' => 'SECRET_OPTION_FEEDBACK',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Monitor',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        return [$assessment, $question, $correctOption];
    }
}
