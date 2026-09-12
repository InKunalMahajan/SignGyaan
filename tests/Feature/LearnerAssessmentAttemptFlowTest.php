<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerAssessmentAttemptFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_learner_can_start_save_resume_review_and_submit_one_attempt(): void
    {
        [$learner, $assessment, $question] = $this->assessmentFixture();

        $start = $this->actingAs($learner)->post(route('learner.assessments.start', $assessment));
        $attempt = $learner->assessmentAttempts()->firstOrFail();
        $start->assertRedirect(route('learner.assessments.attempts.show', $attempt));

        $this->actingAs($learner)->put(route('learner.assessments.attempts.save', $attempt), [
            'responses' => [$question->id => 'B'],
        ])->assertRedirect();

        $this->assertDatabaseHas('assessment_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
        ]);

        $this->actingAs($learner)->post(route('learner.assessments.start', $assessment))
            ->assertRedirect(route('learner.assessments.attempts.show', $attempt));
        $this->assertSame(1, $learner->assessmentAttempts()->count());

        $this->actingAs($learner)->get(route('learner.assessments.attempts.review', $attempt))
            ->assertOk()
            ->assertSee('Review your answers');

        $this->actingAs($learner)->post(route('learner.assessments.attempts.submit', $attempt))
            ->assertRedirect(route('learner.assessments.attempts.result', $attempt));

        $attempt->refresh();
        $this->assertSame('completed', $attempt->status);
        $this->assertNotNull($attempt->submitted_at);
        $this->assertSame('2.00', $attempt->earned_marks);
        $this->assertSame('100.00', $attempt->percentage);
        $this->assertTrue((bool) $attempt->passed);
        $this->assertNotNull(AssessmentAnswer::where('attempt_id', $attempt->id)->first()?->question_snapshot);

        $this->actingAs($learner)->put(route('learner.assessments.attempts.save', $attempt), [
            'responses' => [$question->id => 'A'],
        ])->assertStatus(409);
    }

    public function test_draft_and_unassigned_course_assessments_are_not_accessible(): void
    {
        [$learner, $assessment] = $this->assessmentFixture();
        $assessment->update(['status' => 'draft']);

        $this->actingAs($learner)->post(route('learner.assessments.start', $assessment))->assertNotFound();

        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $otherSubject = Subject::create([
            'created_by' => $otherTeacher->id,
            'name' => 'Other Subject',
            'slug' => 'other-subject-assessment-flow',
            'is_active' => true,
        ]);
        $otherCourse = Course::create([
            'subject_id' => $otherSubject->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Other Course',
            'slug' => 'other-course-assessment-flow',
            'is_active' => true,
        ]);
        $other = Assessment::create([
            'course_id' => $otherCourse->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Other Test',
            'status' => 'published',
            'total_marks' => 2,
            'published_at' => now(),
        ]);

        $this->actingAs($learner)->post(route('learner.assessments.start', $other))->assertForbidden();
    }

    private function assessmentFixture(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'it-learner-assessment-flow',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'IT Assessment Course',
            'slug' => 'it-assessment-course-flow',
            'is_active' => true,
        ]);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC A',
            'code' => 'FYJC-A-ASSESS',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Unit Test 1',
            'status' => 'published',
            'total_marks' => 2,
            'passing_marks' => 1,
            'published_at' => now(),
        ]);
        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'type' => 'single_choice',
            'prompt' => 'Choose B',
            'marks' => 2,
            'position' => 1,
            'config' => ['options' => ['A', 'B']],
            'answer_key' => ['value' => 'B'],
        ]);

        return [$learner, $assessment, $question];
    }
}
