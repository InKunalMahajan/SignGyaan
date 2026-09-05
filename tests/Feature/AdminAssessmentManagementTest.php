<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssessmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_assessment_settings_for_quiz_practice(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quiz = $this->createPractice('quiz');

        $response = $this->actingAs($admin)->post(route('admin.assessments.store'), [
            'practice_resource_id' => $quiz->id,
            'passing_percentage' => 75,
            'max_attempts' => 3,
            'time_limit_minutes' => 20,
            'shuffle_questions' => '1',
            'shuffle_options' => '1',
            'show_feedback' => '1',
            'is_published' => '1',
        ]);

        $assessment = Assessment::query()->firstOrFail();

        $response->assertRedirect(route('admin.assessments.edit', $assessment));
        $this->assertDatabaseHas('assessments', [
            'practice_resource_id' => $quiz->id,
            'passing_percentage' => 75,
            'max_attempts' => 3,
            'time_limit_minutes' => 20,
            'shuffle_questions' => true,
            'shuffle_options' => true,
            'show_feedback' => true,
            'is_published' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.assessments.update', $assessment), [
                'practice_resource_id' => $quiz->id,
                'passing_percentage' => 80,
                'max_attempts' => '',
                'time_limit_minutes' => '',
            ])
            ->assertRedirect(route('admin.assessments.edit', $assessment));

        $assessment->refresh();
        $this->assertSame(80, $assessment->passing_percentage);
        $this->assertNull($assessment->max_attempts);
        $this->assertNull($assessment->time_limit_minutes);
        $this->assertFalse($assessment->shuffle_questions);
        $this->assertFalse($assessment->shuffle_options);
        $this->assertFalse($assessment->show_feedback);
        $this->assertFalse($assessment->is_published);
    }

    public function test_non_admin_cannot_manage_assessments(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('admin.assessments.index'))
            ->assertForbidden();
    }

    public function test_assessment_rejects_ineligible_practice_types_and_duplicate_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reflection = $this->createPractice('reflection');
        $quiz = $this->createPractice('quiz', 'Second Quiz');

        $this->actingAs($admin)
            ->post(route('admin.assessments.store'), [
                'practice_resource_id' => $reflection->id,
                'passing_percentage' => 70,
            ])
            ->assertSessionHasErrors('practice_resource_id');

        Assessment::create([
            'practice_resource_id' => $quiz->id,
            'passing_percentage' => 70,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.assessments.store'), [
                'practice_resource_id' => $quiz->id,
                'passing_percentage' => 70,
            ])
            ->assertSessionHasErrors('practice_resource_id');
    }

    public function test_assessment_with_attempts_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'learner']);
        $quiz = $this->createPractice('quiz');
        $assessment = Assessment::create([
            'practice_resource_id' => $quiz->id,
            'passing_percentage' => 70,
        ]);

        AssessmentAttempt::create([
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

        $this->actingAs($admin)
            ->delete(route('admin.assessments.destroy', $assessment))
            ->assertSessionHasErrors('assessment');

        $this->assertDatabaseHas('assessments', ['id' => $assessment->id]);
    }

    public function test_practice_item_linked_to_assessment_cannot_be_deleted_or_changed_to_resource(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quiz = $this->createPractice('quiz');

        Assessment::create([
            'practice_resource_id' => $quiz->id,
            'passing_percentage' => 70,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.practice.destroy', $quiz))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('practice_resources', ['id' => $quiz->id]);

        $this->actingAs($admin)
            ->put(route('admin.practice.update', $quiz), [
                'lesson_id' => $quiz->lesson_id,
                'title' => $quiz->title,
                'slug' => $quiz->slug,
                'kind' => 'resource',
                'resource_type' => 'worksheet',
                'sort_order' => $quiz->sort_order,
            ])
            ->assertSessionHasErrors('resource_type');
    }

    private function createPractice(string $resourceType, string $title = 'Lesson Check'): PracticeResource
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

        return PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => $title,
            'slug' => str($title)->slug()->value(),
            'kind' => 'practice',
            'resource_type' => $resourceType,
            'sort_order' => PracticeResource::query()->count() + 1,
            'is_published' => true,
        ]);
    }
}
