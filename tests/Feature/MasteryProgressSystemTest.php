<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\CourseMastery;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Models\User;
use App\Services\MasteryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasteryProgressSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_mastery_combines_published_lesson_completion_and_weighted_assessment_evidence(): void
    {
        [$teacher, $learner, $course, $class, $lessons] = $this->fixture();

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $lessons[0]->id,
            'status' => 'completed',
            'started_at' => now()->subDay(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Mastery Assessment',
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
            'reviewed_at' => now(),
        ]);

        $mastery = app(MasteryService::class)->calculateFor($learner, $course);

        $this->assertSame(1, $mastery->lessons_completed);
        $this->assertSame(2, $mastery->lessons_total);
        $this->assertSame('50.00', $mastery->lesson_completion_percentage);
        $this->assertSame('80.00', $mastery->assessment_percentage);
        $this->assertSame('68.00', $mastery->mastery_score);
        $this->assertSame('good', $mastery->mastery_level);
        $this->assertSame('assessment_supported', $mastery->evidence_status);
    }

    public function test_draft_lessons_and_inactive_chapters_do_not_count_and_learning_only_mastery_is_provisional(): void
    {
        [, $learner, $course, , $lessons] = $this->fixture();

        foreach ($lessons as $lesson) {
            LessonProgress::create([
                'learner_id' => $learner->id,
                'lesson_id' => $lesson->id,
                'status' => 'completed',
                'started_at' => now(),
                'last_viewed_at' => now(),
                'completed_at' => now(),
            ]);
        }

        $mastery = app(MasteryService::class)->calculateFor($learner, $course);

        $this->assertSame(2, $mastery->lessons_total);
        $this->assertSame('100.00', $mastery->mastery_score);
        $this->assertSame('mastered', $mastery->mastery_level);
        $this->assertSame('learning_only', $mastery->evidence_status);
        $this->assertNull($mastery->assessment_percentage);
    }

    public function test_mastery_band_boundaries_are_stable(): void
    {
        $service = app(MasteryService::class);

        $this->assertSame('needs_support', $service->levelFor(39));
        $this->assertSame('developing', $service->levelFor(40));
        $this->assertSame('developing', $service->levelFor(59.99));
        $this->assertSame('good', $service->levelFor(60));
        $this->assertSame('good', $service->levelFor(79.99));
        $this->assertSame('mastered', $service->levelFor(80));
        $this->assertSame('mastered', $service->levelFor(100));
    }

    public function test_learner_progress_routes_only_expose_assigned_courses(): void
    {
        [$teacher, $learner, $course] = $this->fixture();

        $this->actingAs($learner)
            ->get(route('learner.progress.index'))
            ->assertOk()
            ->assertSee($course->title);

        $otherSubject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Other Subject',
            'slug' => 'other-subject-mastery',
            'is_active' => true,
        ]);
        $otherCourse = Course::create([
            'subject_id' => $otherSubject->id,
            'created_by' => $teacher->id,
            'title' => 'Unassigned Course',
            'slug' => 'unassigned-course-mastery',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get(route('learner.progress.courses.show', $otherCourse))
            ->assertForbidden();
    }

    public function test_teacher_can_view_own_learner_progress_but_not_unrelated_learner(): void
    {
        [$teacher, $learner] = $this->fixture();
        $otherLearner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($teacher)
            ->get(route('teacher.progress.index'))
            ->assertOk()
            ->assertSee($learner->name);

        $this->actingAs($teacher)
            ->get(route('teacher.progress.learners.show', $learner))
            ->assertOk();

        $this->actingAs($teacher)
            ->get(route('teacher.progress.learners.show', $otherLearner))
            ->assertForbidden();
    }

    public function test_recommendations_point_to_incomplete_learning_and_low_assessment_review(): void
    {
        [$teacher, $learner, $course] = $this->fixture();

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'created_by' => $teacher->id,
            'title' => 'Low Score Assessment',
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
            'earned_marks' => 35,
            'total_marks_snapshot' => 100,
            'passing_marks_snapshot' => 40,
            'percentage' => 35,
            'passed' => false,
            'started_at' => now()->subHour(),
            'submitted_at' => now()->subMinutes(30),
            'reviewed_at' => now(),
        ]);

        $snapshot = app(MasteryService::class)->snapshot($learner, $course);
        $types = collect($snapshot['recommendations'])->pluck('type');

        $this->assertTrue($types->contains('lesson'));
        $this->assertTrue($types->contains('assessment_review'));
    }

    private function fixture(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'it-mastery-progress',
            'is_active' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'IT Mastery Course',
            'slug' => 'it-mastery-course',
            'is_active' => true,
        ]);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC A',
            'code' => 'FYJC-A-MASTERY',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Active Chapter',
            'position' => 1,
            'is_active' => true,
        ]);
        $lessonOne = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Lesson One',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);
        $lessonTwo = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Lesson Two',
            'position' => 2,
            'status' => 'published',
            'published_at' => now(),
        ]);
        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Draft Lesson',
            'position' => 3,
            'status' => 'draft',
        ]);

        $inactiveUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Inactive Chapter',
            'position' => 2,
            'is_active' => false,
        ]);
        Lesson::create([
            'course_unit_id' => $inactiveUnit->id,
            'title' => 'Hidden Published Lesson',
            'position' => 1,
            'status' => 'published',
            'published_at' => now(),
        ]);

        return [$teacher, $learner, $course, $class, [$lessonOne, $lessonTwo]];
    }
}
