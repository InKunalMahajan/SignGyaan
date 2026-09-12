<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAssessmentAuthoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_question_and_publish_valid_assessment(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Author');

        $response = $this->actingAs($teacher)->post(route('teacher.assessments.store'), [
            'course_id' => $course->id,
            'title' => 'Unit Test 1',
            'instructions' => 'Choose the correct answer.',
            'total_marks' => 2,
            'passing_marks' => 1,
            'duration_minutes' => 30,
        ]);

        $assessment = Assessment::where('title', 'Unit Test 1')->firstOrFail();
        $response->assertRedirect(route('teacher.assessments.edit', $assessment));
        $this->assertSame('draft', $assessment->status);

        $this->actingAs($teacher)->post(route('teacher.assessments.questions.store', $assessment), [
            'type' => 'single_choice',
            'prompt' => 'Which device is an input device?',
            'marks' => 2,
            'options' => "Keyboard\nMonitor\nPrinter",
            'correct_answers' => 'Keyboard',
        ])->assertRedirect();

        $question = $assessment->questions()->firstOrFail();
        $this->assertSame(['options' => ['Keyboard', 'Monitor', 'Printer']], $question->config);
        $this->assertSame(['value' => 'Keyboard'], $question->answer_key);

        $this->actingAs($teacher)
            ->patch(route('teacher.assessments.publish', $assessment))
            ->assertRedirect();

        $assessment->refresh();
        $this->assertSame('published', $assessment->status);
        $this->assertNotNull($assessment->published_at);
    }

    public function test_teacher_cannot_author_assessment_for_another_teachers_course(): void
    {
        [$teacher] = $this->teacherWithCourse('Owner A');
        [, $otherCourse] = $this->teacherWithCourse('Owner B');

        $this->actingAs($teacher)->post(route('teacher.assessments.store'), [
            'course_id' => $otherCourse->id,
            'title' => 'Not Allowed',
            'total_marks' => 10,
        ])->assertNotFound();

        $this->assertDatabaseMissing('assessments', ['title' => 'Not Allowed']);
    }

    public function test_publish_requires_question_marks_to_equal_assessment_total(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Marks');
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Mismatch Test',
            'status' => 'draft',
            'total_marks' => 10,
        ]);
        $assessment->questions()->create([
            'type' => 'true_false',
            'prompt' => 'A keyboard is an input device.',
            'marks' => 2,
            'position' => 1,
            'config' => [],
            'answer_key' => ['value' => true],
        ]);

        $this->actingAs($teacher)
            ->from(route('teacher.assessments.edit', $assessment))
            ->patch(route('teacher.assessments.publish', $assessment))
            ->assertSessionHasErrors('publish');

        $this->assertSame('draft', $assessment->fresh()->status);
    }

    public function test_existing_attempt_locks_assessment_structure(): void
    {
        [$teacher, $course] = $this->teacherWithCourse('Locked');
        $learner = User::factory()->create(['role' => 'learner']);
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Locked Test',
            'status' => 'published',
            'total_marks' => 2,
            'published_at' => now(),
        ]);
        $question = $assessment->questions()->create([
            'type' => 'true_false',
            'prompt' => 'Original prompt',
            'marks' => 2,
            'position' => 1,
            'config' => [],
            'answer_key' => ['value' => true],
        ]);
        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'in_progress',
            'total_marks_snapshot' => 2,
            'started_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->from(route('teacher.assessments.edit', $assessment))
            ->put(route('teacher.assessments.questions.update', [$assessment, $question]), [
                'type' => 'true_false',
                'prompt' => 'Changed prompt',
                'marks' => 2,
                'position' => 1,
                'correct_answers' => 'True',
            ])
            ->assertSessionHasErrors('assessment');

        $this->assertSame('Original prompt', $question->fresh()->prompt);
    }

    private function teacherWithCourse(string $suffix): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => "IT {$suffix}",
            'slug' => 'it-'.strtolower(str_replace(' ', '-', $suffix)).'-'.uniqid(),
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => "Course {$suffix}",
            'slug' => 'course-'.strtolower(str_replace(' ', '-', $suffix)).'-'.uniqid(),
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        return [$teacher, $course];
    }
}
