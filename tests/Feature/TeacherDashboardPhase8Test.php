<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\CourseMastery;
use App\Models\LearningClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherDashboardPhase8Test extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_shows_only_owned_teaching_data_and_real_workload(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $otherLearner = User::factory()->create(['role' => 'learner']);

        $class = $this->makeClass($teacher, 'FYJC A', 'SG-FYJCA');
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $otherClass = $this->makeClass($otherTeacher, 'Other Teacher Class', 'SG-OTHER');
        $otherClass->learners()->attach($otherLearner->id, ['enrolled_at' => now()]);

        $course = $this->makeCourse($teacher, 'Information Technology');
        $otherCourse = $this->makeCourse($otherTeacher, 'Other Teacher Course');
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);
        $otherClass->courses()->attach($otherCourse->id, ['assigned_by' => $otherTeacher->id, 'assigned_at' => now()]);

        CourseMastery::create([
            'learner_id' => $learner->id,
            'course_id' => $course->id,
            'lessons_completed' => 1,
            'lessons_total' => 4,
            'lesson_completion_percentage' => 25,
            'assessments_completed' => 1,
            'assessment_percentage' => 35,
            'mastery_score' => 31,
            'mastery_level' => 'needs_support',
            'evidence_status' => 'assessment_supported',
            'last_calculated_at' => now(),
        ]);

        CourseMastery::create([
            'learner_id' => $otherLearner->id,
            'course_id' => $otherCourse->id,
            'lessons_completed' => 4,
            'lessons_total' => 4,
            'lesson_completion_percentage' => 100,
            'assessments_completed' => 1,
            'assessment_percentage' => 100,
            'mastery_score' => 100,
            'mastery_level' => 'mastered',
            'evidence_status' => 'assessment_supported',
            'last_calculated_at' => now(),
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Skills Check',
            'status' => 'published',
            'total_marks' => 10,
            'passing_marks' => 4,
            'published_at' => now(),
        ]);

        $otherAssessment = Assessment::create([
            'course_id' => $otherCourse->id,
            'created_by' => $otherTeacher->id,
            'title' => 'Other Teacher Assessment',
            'status' => 'published',
            'total_marks' => 10,
            'passing_marks' => 4,
            'published_at' => now(),
        ]);

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'pending_review',
            'total_marks_snapshot' => 10,
            'passing_marks_snapshot' => 4,
            'started_at' => now()->subHour(),
            'submitted_at' => now(),
        ]);

        AssessmentAttempt::create([
            'assessment_id' => $otherAssessment->id,
            'learner_id' => $otherLearner->id,
            'attempt_number' => 1,
            'status' => 'pending_review',
            'total_marks_snapshot' => 10,
            'passing_marks_snapshot' => 4,
            'started_at' => now()->subHour(),
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get(route('dashboard.role', 'teacher'));

        $response
            ->assertOk()
            ->assertSee('Teaching at a glance')
            ->assertSee('FYJC A')
            ->assertSee('Digital Skills Check')
            ->assertSee('Pending reviews')
            ->assertSee('Needs Support')
            ->assertSee($learner->name)
            ->assertDontSee('Other Teacher Class')
            ->assertDontSee('Other Teacher Assessment')
            ->assertDontSee($otherLearner->name);
    }

    public function test_teacher_dashboard_counts_unique_active_learners_across_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $classA = $this->makeClass($teacher, 'FYJC A', 'SG-A');
        $classB = $this->makeClass($teacher, 'FYJC B', 'SG-B');

        $classA->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $classB->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $response = $this->actingAs($teacher)->get(route('dashboard.role', 'teacher'));

        $response
            ->assertOk()
            ->assertSee('2')
            ->assertSee('1 unique active learner');
    }

    public function test_teacher_dashboard_has_clear_empty_state_and_navigation(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('dashboard.role', 'teacher'));

        $response
            ->assertOk()
            ->assertSee('No active classes yet.')
            ->assertSee('No assessment attempts are waiting for manual review.')
            ->assertSee('No assessments yet.')
            ->assertSee('Create Class')
            ->assertSee('Create Assessment')
            ->assertSee('View Learner Progress');
    }

    private function makeClass(User $teacher, string $name, string $code): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'code' => $code,
            'subject' => 'Information Technology',
            'level' => '11',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
    }

    private function makeCourse(User $teacher, string $title): Course
    {
        $slug = str($title)->slug()->append('-'.$teacher->id)->toString();
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => $title.' Subject',
            'slug' => $slug.'-subject',
            'is_active' => true,
        ]);

        return Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => $title,
            'slug' => $slug,
            'level' => 'Beginner',
            'is_active' => true,
        ]);
    }
}
