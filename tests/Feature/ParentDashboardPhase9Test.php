<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\CourseMastery;
use App\Models\LearningClass;
use App\Models\ParentLearnerLink;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentDashboardPhase9Test extends TestCase
{
    use RefreshDatabase;

    public function test_parent_dashboard_shows_only_approved_linked_learner_progress(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true, 'name' => 'Approved Learner']);
        $pendingLearner = User::factory()->create(['role' => 'learner', 'is_active' => true, 'name' => 'Pending Learner']);
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        $approved = ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'approved',
            'responded_at' => now(),
            'approved_at' => now(),
        ]);

        ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $pendingLearner->id,
            'relationship' => 'guardian',
            'status' => 'pending',
        ]);

        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC A',
            'code' => 'FYJC-A',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'name' => 'Information Technology',
            'slug' => 'information-technology',
            'created_by' => $teacher->id,
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'IT Course',
            'slug' => 'it-course',
            'level' => 'FYJC',
            'is_active' => true,
        ]);

        CourseMastery::create([
            'learner_id' => $learner->id,
            'course_id' => $course->id,
            'lessons_completed' => 2,
            'lessons_total' => 5,
            'lesson_completion_percentage' => 40,
            'assessments_completed' => 1,
            'assessment_percentage' => 35,
            'mastery_score' => 37,
            'mastery_level' => 'needs_support',
            'evidence_status' => 'complete',
            'last_calculated_at' => now(),
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Unit Test 1',
            'status' => 'published',
            'total_marks' => 20,
            'passing_marks' => 8,
            'published_at' => now(),
        ]);

        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'earned_marks' => 14,
            'total_marks_snapshot' => 20,
            'passing_marks_snapshot' => 8,
            'percentage' => 70,
            'passed' => true,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($parent)->get(route('dashboard.role', 'parents'));

        $response->assertOk()
            ->assertSee('Parent Dashboard')
            ->assertSee('Approved Learner')
            ->assertDontSee('Pending Learner')
            ->assertSee('Needs Support')
            ->assertSee('Unit Test 1')
            ->assertSee('70%');

        $this->actingAs($parent)
            ->get(route('parents.learners.show', $approved))
            ->assertOk()
            ->assertSee('Mastery by Course')
            ->assertSee('Recent Assessments')
            ->assertSee('IT Course');
    }

    public function test_pending_review_is_not_counted_as_completed_assessment_average(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);
        $teacher = User::factory()->create(['role' => 'teacher', 'is_active' => true]);

        ParentLearnerLink::create([
            'parent_user_id' => $parent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'approved',
            'responded_at' => now(),
            'approved_at' => now(),
        ]);

        $subject = Subject::create([
            'name' => 'Computer',
            'slug' => 'computer',
            'created_by' => $teacher->id,
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'PY',
            'is_active' => true,
        ]);
        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Manual Review Test',
            'status' => 'published',
            'total_marks' => 10,
            'published_at' => now(),
        ]);
        AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'learner_id' => $learner->id,
            'attempt_number' => 1,
            'status' => 'pending_review',
            'total_marks_snapshot' => 10,
            'started_at' => now()->subHour(),
            'submitted_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get(route('dashboard.role', 'parents'))
            ->assertOk()
            ->assertSee('pending review')
            ->assertSee('Completed results only');
    }

    public function test_parent_cannot_view_another_parents_linked_learner(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);
        $otherParent = User::factory()->create(['role' => 'parents', 'is_active' => true]);
        $learner = User::factory()->create(['role' => 'learner', 'is_active' => true]);

        $link = ParentLearnerLink::create([
            'parent_user_id' => $otherParent->id,
            'learner_user_id' => $learner->id,
            'relationship' => 'parent',
            'status' => 'approved',
            'responded_at' => now(),
            'approved_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get(route('parents.learners.show', $link))
            ->assertForbidden();
    }

    public function test_parent_dashboard_has_usable_empty_state_without_approved_links(): void
    {
        $parent = User::factory()->create(['role' => 'parents', 'is_active' => true]);

        $this->actingAs($parent)
            ->get(route('dashboard.role', 'parents'))
            ->assertOk()
            ->assertSee('No approved learner link yet')
            ->assertSee('Open Parent Profile');
    }
}
