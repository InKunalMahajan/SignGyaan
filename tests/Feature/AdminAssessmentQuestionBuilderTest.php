<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
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

class AdminAssessmentQuestionBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_single_choice_question_with_options(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $assessment = $this->createAssessment();

        $response = $this->actingAs($admin)->post(
            route('admin.assessments.questions.store', $assessment),
            [
                'question_type' => 'single-choice',
                'prompt' => 'Which device is used to type text?',
                'explanation' => 'A keyboard is an input device used for typing.',
                'points' => 2,
                'sort_order' => 1,
                'is_required' => '1',
                'is_published' => '1',
                'options' => [
                    ['option_text' => 'Keyboard', 'feedback' => 'Correct', 'is_correct' => '1'],
                    ['option_text' => 'Monitor', 'feedback' => 'A monitor displays output.'],
                    ['option_text' => 'Speaker', 'feedback' => 'A speaker produces sound.'],
                ],
            ]
        );

        $question = AssessmentQuestion::query()->firstOrFail();

        $response->assertRedirect(route('admin.assessments.questions.index', $assessment));
        $this->assertSame('single-choice', $question->question_type);
        $this->assertSame('2.00', $question->points);
        $this->assertTrue($question->is_required);
        $this->assertTrue($question->is_published);
        $this->assertCount(3, $question->options);
        $this->assertSame(1, $question->options->where('is_correct', true)->count());
        $this->assertSame('Keyboard', $question->options->firstWhere('is_correct', true)->option_text);
    }

    public function test_single_choice_requires_exactly_one_correct_option(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $assessment = $this->createAssessment();

        $this->actingAs($admin)
            ->post(route('admin.assessments.questions.store', $assessment), [
                'question_type' => 'single-choice',
                'prompt' => 'Choose one answer.',
                'points' => 1,
                'sort_order' => 1,
                'options' => [
                    ['option_text' => 'Option A', 'is_correct' => '1'],
                    ['option_text' => 'Option B', 'is_correct' => '1'],
                ],
            ])
            ->assertSessionHasErrors('options');

        $this->assertDatabaseCount('assessment_questions', 0);
    }

    public function test_admin_can_create_true_false_and_fill_blank_questions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $assessment = $this->createAssessment();

        $this->actingAs($admin)
            ->post(route('admin.assessments.questions.store', $assessment), [
                'question_type' => 'true-false',
                'prompt' => 'A keyboard is an input device.',
                'true_false_answer' => 'true',
                'points' => 1,
                'sort_order' => 1,
                'is_required' => '1',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.assessments.questions.index', $assessment));

        $trueFalse = AssessmentQuestion::query()->where('question_type', 'true-false')->firstOrFail();
        $this->assertCount(2, $trueFalse->options);
        $this->assertSame('True', $trueFalse->options->firstWhere('is_correct', true)->option_text);

        $this->actingAs($admin)
            ->post(route('admin.assessments.questions.store', $assessment), [
                'question_type' => 'fill-blank',
                'prompt' => 'The device used to type is called a _____.',
                'accepted_answers' => "keyboard\nKeyboard\nkey board",
                'points' => 2,
                'sort_order' => 2,
                'is_required' => '1',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.assessments.questions.index', $assessment));

        $fillBlank = AssessmentQuestion::query()->where('question_type', 'fill-blank')->firstOrFail();
        $this->assertCount(0, $fillBlank->options);
        $this->assertSame(['keyboard', 'key board'], $fillBlank->answer_key['accepted_answers']);
    }

    public function test_question_routes_reject_question_from_another_assessment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstAssessment = $this->createAssessment('First Quiz');
        $secondAssessment = $this->createAssessment('Second Quiz');

        $question = AssessmentQuestion::create([
            'assessment_id' => $secondAssessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'Private question',
            'answer_key' => ['accepted_answers' => ['answer']],
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.assessments.questions.edit', [$firstAssessment, $question]))
            ->assertNotFound();
    }

    public function test_answered_question_cannot_change_scoring_configuration_or_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'learner']);
        $assessment = $this->createAssessment();

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'question_type' => 'fill-blank',
            'prompt' => 'Type keyboard.',
            'answer_key' => ['accepted_answers' => ['keyboard']],
            'points' => 1,
            'sort_order' => 1,
            'is_required' => true,
            'is_published' => true,
        ]);

        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => $learner->id,
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
            'response' => ['text' => 'keyboard'],
            'question_snapshot' => $question->prompt,
            'is_correct' => true,
            'points_awarded' => 1,
            'answered_at' => now(),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.assessments.questions.update', [$assessment, $question]), [
                'question_type' => 'fill-blank',
                'prompt' => 'Type keyboard.',
                'accepted_answers' => "keyboard\nkey board",
                'points' => 1,
                'sort_order' => 1,
                'is_required' => '1',
                'is_published' => '1',
            ])
            ->assertSessionHasErrors('question_type');

        $this->actingAs($admin)
            ->delete(route('admin.assessments.questions.destroy', [$assessment, $question]))
            ->assertSessionHasErrors('question');

        $this->assertDatabaseHas('assessment_questions', ['id' => $question->id]);
    }

    public function test_learner_cannot_access_question_builder(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        $assessment = $this->createAssessment();

        $this->actingAs($learner)
            ->get(route('admin.assessments.questions.index', $assessment))
            ->assertForbidden();
    }

    private function createAssessment(string $title = 'Lesson Check'): Assessment
    {
        $subject = Subject::firstOrCreate(
            ['slug' => 'digital-skills'],
            ['name' => 'Digital Skills', 'sort_order' => 1, 'is_published' => true]
        );

        $course = Course::firstOrCreate(
            ['subject_id' => $subject->id, 'slug' => 'computer-basics'],
            ['title' => 'Computer Basics', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => true]
        );

        $unit = Unit::firstOrCreate(
            ['course_id' => $course->id, 'slug' => 'foundations'],
            ['title' => 'Foundations', 'sort_order' => 1, 'is_published' => true]
        );

        $lesson = Lesson::firstOrCreate(
            ['unit_id' => $unit->id, 'slug' => 'introduction'],
            ['title' => 'Introduction', 'sort_order' => 1, 'is_published' => true]
        );

        $practice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => $title,
            'slug' => str($title)->slug()->append('-'.uniqid())->value(),
            'kind' => 'practice',
            'resource_type' => 'quiz',
            'sort_order' => PracticeResource::query()->count() + 1,
            'is_published' => true,
        ]);

        return Assessment::create([
            'practice_resource_id' => $practice->id,
            'passing_percentage' => 70,
            'show_feedback' => true,
            'is_published' => false,
        ]);
    }
}
