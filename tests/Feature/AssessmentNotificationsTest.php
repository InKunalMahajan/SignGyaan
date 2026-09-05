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

class AssessmentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_passing_submission_creates_one_result_notification(): void
    {
        [$assessment, $question, $correctOption] = $this->assessment();
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

        $notifications = $learner->fresh()->notifications;

        $this->assertCount(1, $notifications);
        $this->assertSame('assessment', $notifications->first()->data['category']);
        $this->assertSame('Assessment passed', $notifications->first()->data['title']);
        $this->assertSame('Review Result', $notifications->first()->data['action_label']);
        $this->assertTrue($notifications->first()->data['meta']['passed']);
    }

    public function test_failed_submission_creates_result_and_retry_notifications_when_attempts_remain(): void
    {
        [$assessment, $question, $correctOption, $wrongOption] = $this->assessment(maxAttempts: 3);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
            'answers' => [
                $question->id => ['option_ids' => [$wrongOption->id]],
            ],
        ]);

        $notifications = $learner->fresh()->notifications;

        $this->assertCount(2, $notifications);
        $this->assertTrue($notifications->contains(fn ($notification) => $notification->data['title'] === 'Assessment result ready'));
        $this->assertTrue($notifications->contains(fn ($notification) => $notification->data['title'] === 'Another attempt is available'));

        $retry = $notifications->first(fn ($notification) => $notification->data['title'] === 'Another attempt is available');
        $this->assertSame('Try Again', $retry->data['action_label']);
        $this->assertSame(2, $retry->data['meta']['attempts_remaining']);
    }

    public function test_failed_submission_does_not_offer_retry_when_attempt_limit_is_reached(): void
    {
        [$assessment, $question, $correctOption, $wrongOption] = $this->assessment(maxAttempts: 1);
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $this->actingAs($learner)->post(route('assessment-attempts.submit', [$assessment, $attempt]), [
            'answers' => [
                $question->id => ['option_ids' => [$wrongOption->id]],
            ],
        ]);

        $notifications = $learner->fresh()->notifications;

        $this->assertCount(1, $notifications);
        $this->assertSame('Assessment result ready', $notifications->first()->data['title']);
        $this->assertSame(0, $notifications->first()->data['meta']['attempts_remaining']);
    }

    public function test_result_notifications_are_not_duplicated_for_same_attempt(): void
    {
        [$assessment, $question, $correctOption] = $this->assessment();
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)->post(route('assessments.start', $assessment));
        $attempt = $assessment->attempts()->where('user_id', $learner->id)->firstOrFail();

        $payload = [
            'answers' => [
                $question->id => ['option_ids' => [$correctOption->id]],
            ],
        ];

        $this->actingAs($learner)->post(route('assessment-attempts.submit', [$assessment, $attempt]), $payload);
        $this->actingAs($learner)->post(route('assessment-attempts.submit', [$assessment, $attempt]), $payload);

        $this->assertCount(1, $learner->fresh()->notifications);
    }

    private function assessment(int $maxAttempts = 3): array
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
            'prompt' => 'Which device is used to type text?',
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $correctOption = AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Keyboard',
            'is_correct' => true,
            'sort_order' => 1,
        ]);

        $wrongOption = AssessmentOption::create([
            'assessment_question_id' => $question->id,
            'option_text' => 'Monitor',
            'is_correct' => false,
            'sort_order' => 2,
        ]);

        return [$assessment, $question, $correctOption, $wrongOption];
    }
}
