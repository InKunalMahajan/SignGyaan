<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerDashboardPhase7Test extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_uses_real_learning_mastery_assessment_and_continue_data(): void
    {
        [$teacher, $learner, $course, $class, $lessonOne, $lessonTwo] = $this->fixture();

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lessonOne->id,
            'status' => 'completed',
            'started_at' => now()->subDays(2),
            'last_viewed_at' => now()->subDay(),
            'completed_at' => now()->subDay(),
        ]);
        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lessonTwo->id,
            'status' => 'in_progress',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subMinutes(5),
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Unit Test 1',
            'status' => 'published',
            'total_marks' => 100,
            'passing_marks' => 40,
            'published_at' => now(),
        ]);
        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'earned_marks' => 80,
            'total_marks_snapshot' => 100,
            'passing_marks_snapshot' => 40,
            'percentage' => 80,
            'passed' => true,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'reviewed_at' => now()->subMinutes(20),
        ]);

        $response = $this->actingAs($learner)->get(route('dashboard.role', 'learner'));

        $response->assertOk()
            ->assertSee('Welcome, '.$learner->name)
            ->assertSee('Resume Lesson')
            ->assertSee($lessonTwo->title)
            ->assertSee($course->title)
            ->assertSee('68%')
            ->assertSee('80%')
            ->assertSee('Unit Test 1')
            ->assertSee('Live data')
            ->assertDontSee('Demo values');
    }

    public function test_dashboard_excludes_draft_inactive_and_unassigned_learning_content(): void
    {
        [$teacher, $learner, $course] = $this->fixture();

        $draftUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Draft Content Chapter',
            'position' => 8,
            'is_active' => true,
        ]);
        Lesson::create([
            'course_unit_id' => $draftUnit->id,
            'title' => 'Hidden Draft Lesson',
            'position' => 1,
            'status' => 'draft',
        ]);

        $inactiveUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Inactive Chapter',
            'position' => 9,
            'is_active' => false,
        ]);
        Lesson::create([
            'course_unit_id' => $inactiveUnit->id,
            'title' => 'Hidden Inactive Lesson',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $otherSubject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Unassigned Subject',
            'slug' => 'unassigned-dashboard-subject',
            'is_active' => true,
        ]);
        $otherCourse = Course::create([
            'subject_id' => $otherSubject->id,
            'created_by' => $teacher->id,
            'title' => 'Unassigned Dashboard Course',
            'slug' => 'unassigned-dashboard-course',
            'is_active' => true,
        ]);
        Assessment::create([
            'course_id' => $otherCourse->id,
            'created_by' => $teacher->id,
            'title' => 'Hidden Unassigned Assessment',
            'status' => 'published',
            'total_marks' => 10,
            'published_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'))
            ->assertOk()
            ->assertDontSee('Hidden Draft Lesson')
            ->assertDontSee('Hidden Inactive Lesson')
            ->assertDontSee('Unassigned Dashboard Course')
            ->assertDontSee('Hidden Unassigned Assessment');
    }

    public function test_dashboard_has_useful_empty_state_when_learner_has_no_active_assignments(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'))
            ->assertOk()
            ->assertSee('No lesson ready yet')
            ->assertSee('No active courses yet')
            ->assertSee('No published assessments are available yet')
            ->assertSee('My Classes')
            ->assertSee('My Progress');
    }

    private function fixture(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'phase7-dashboard-it',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Phase 7 IT Course',
            'slug' => 'phase7-it-course',
            'is_active' => true,
        ]);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC A',
            'code' => 'FYJC-A-PHASE7',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Dashboard Chapter',
            'position' => 1,
            'is_active' => true,
        ]);
        $lessonOne = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Completed Lesson',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);
        $lessonTwo = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'In Progress Lesson',
            'position' => 2,
            'status' => 'published',
            'published_at' => now(),
        ]);

        return [$teacher, $learner, $course, $class, $lessonOne, $lessonTwo];
    }
}
