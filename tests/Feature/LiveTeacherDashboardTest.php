<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LiveTeacherDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_shows_live_class_and_completion_metrics(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $firstLearner = User::factory()->create(['role' => 'learner', 'name' => 'First Learner']);
        $secondLearner = User::factory()->create(['role' => 'learner', 'name' => 'Second Learner']);

        [$class, $course, $unit, $firstLesson] = $this->makeLearningPath($teacher, $firstLearner);
        $class->learners()->attach($secondLearner->id, ['enrolled_at' => now()]);
        $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $firstLearner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subMinutes(30),
            'completed_at' => now()->subMinutes(30),
        ]);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertOk();
        $response->assertViewIs('teacher.dashboard');
        $response->assertViewHas('activeClassCount', 1);
        $response->assertViewHas('uniqueLearnerCount', 2);
        $response->assertViewHas('activeCourseCount', 1);
        $response->assertViewHas('publishedLessonCount', 2);
        $response->assertViewHas('averageCompletion', 25);
        $response->assertViewHas('needsAttentionLearnerCount', 1);
        $response->assertSee('Digital Skills');
        $response->assertSee('First Learner');
    }

    public function test_support_signal_uses_below_fifty_percent_threshold(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learnerAtFifty = User::factory()->create(['role' => 'learner', 'name' => 'Fifty Learner']);
        $learnerBelow = User::factory()->create(['role' => 'learner', 'name' => 'Below Learner']);

        [$class, $course, $unit, $firstLesson] = $this->makeLearningPath($teacher, $learnerAtFifty);
        $class->learners()->attach($learnerBelow->id, ['enrolled_at' => now()]);
        $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learnerAtFifty->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertViewHas('needsAttentionLearnerCount', 1);
        $response->assertViewHas('supportSignals', function ($signals) use ($learnerAtFifty, $learnerBelow) {
            $ids = $signals->map(fn ($signal) => $signal['learner']->id);

            return $ids->contains($learnerBelow->id)
                && ! $ids->contains($learnerAtFifty->id);
        });
    }

    public function test_dashboard_excludes_draft_hidden_and_inactive_course_lessons(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        [$class, $course, $unit, $visibleLesson] = $this->makeLearningPath($teacher, $learner);

        Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Draft Lesson',
            'position' => 2,
            'status' => 'draft',
        ]);

        $hiddenUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Hidden Unit',
            'position' => 2,
            'is_active' => false,
        ]);
        $this->makeLesson($hiddenUnit, 'Hidden Lesson', 1);

        $inactiveSubject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Inactive Subject',
            'slug' => 'inactive-subject',
            'is_active' => true,
        ]);
        $inactiveCourse = Course::create([
            'subject_id' => $inactiveSubject->id,
            'created_by' => $teacher->id,
            'title' => 'Inactive Course',
            'slug' => 'inactive-course',
            'is_active' => false,
        ]);
        $class->courses()->attach($inactiveCourse->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);
        $inactiveUnit = CourseUnit::create([
            'course_id' => $inactiveCourse->id,
            'title' => 'Inactive Course Unit',
            'position' => 1,
            'is_active' => true,
        ]);
        $this->makeLesson($inactiveUnit, 'Inactive Course Lesson', 1);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertViewHas('publishedLessonCount', 1);
        $response->assertViewHas('activeCourseCount', 1);
        $response->assertViewHas('classCards', function ($cards) use ($visibleLesson) {
            $summary = $cards->first();

            return $summary
                && $summary['published_lesson_count'] === 1
                && $summary['lesson_ids']->contains($visibleLesson->id);
        });
    }

    public function test_shared_course_and_learner_are_deduplicated_across_active_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        [$firstClass, $course] = $this->makeLearningPath($teacher, $learner);

        $secondClass = $this->makeClass($teacher, 'Second Class');
        $secondClass->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $secondClass->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertViewHas('activeClassCount', 2);
        $response->assertViewHas('uniqueLearnerCount', 1);
        $response->assertViewHas('activeCourseCount', 1);
        $response->assertViewHas('publishedLessonCount', 1);
    }

    public function test_archived_classes_are_shown_but_excluded_from_live_totals_and_support_signals(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner', 'name' => 'Archived Learner']);
        [$class] = $this->makeLearningPath($teacher, $learner);
        $class->update(['is_active' => false]);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertOk();
        $response->assertSee($class->name);
        $response->assertViewHas('activeClassCount', 0);
        $response->assertViewHas('uniqueLearnerCount', 0);
        $response->assertViewHas('publishedLessonCount', 0);
        $response->assertViewHas('needsAttentionLearnerCount', 0);
    }

    public function test_teacher_dashboard_does_not_include_another_teachers_classes_or_activity(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $ownLearner = User::factory()->create(['role' => 'learner', 'name' => 'Own Learner']);
        $otherLearner = User::factory()->create(['role' => 'learner', 'name' => 'Other Learner']);

        [$ownClass] = $this->makeLearningPath($teacher, $ownLearner, 'Own Class', 'Own Course');
        [$otherClass, $otherCourse, $otherUnit, $otherLesson] = $this->makeLearningPath($otherTeacher, $otherLearner, 'Other Class', 'Other Course');

        LessonProgress::create([
            'learner_id' => $otherLearner->id,
            'lesson_id' => $otherLesson->id,
            'status' => 'in_progress',
            'started_at' => now(),
            'last_viewed_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertSee($ownClass->name);
        $response->assertDontSee($otherClass->name);
        $response->assertDontSee($otherLearner->name);
        $response->assertViewHas('activeClassCount', 1);
    }

    public function test_empty_teacher_dashboard_has_zero_live_metrics(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get('/dashboard/teacher');

        $response->assertOk();
        $response->assertViewHas('activeClassCount', 0);
        $response->assertViewHas('uniqueLearnerCount', 0);
        $response->assertViewHas('activeCourseCount', 0);
        $response->assertViewHas('publishedLessonCount', 0);
        $response->assertViewHas('averageCompletion', 0);
        $response->assertSee('No classes yet');
    }

    public function test_normal_dashboard_redirect_reaches_the_live_teacher_dashboard_url(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get('/dashboard')
            ->assertRedirect('/dashboard/teacher');

        $this->actingAs($teacher)
            ->get('/dashboard/teacher')
            ->assertOk()
            ->assertViewIs('teacher.dashboard');
    }

    public function test_non_teacher_cannot_open_live_teacher_dashboard(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get('/dashboard/teacher')
            ->assertForbidden();
    }

    public function test_existing_class_progress_report_still_uses_the_same_live_calculation(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        [$class, $course, $unit, $firstLesson] = $this->makeLearningPath($teacher, $learner);
        $this->makeLesson($unit, 'Second Lesson', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $firstLesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'last_viewed_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.classes.progress', $class))
            ->assertOk()
            ->assertSee('50%')
            ->assertSee('1 / 2 completed');
    }

    private function makeLearningPath(
        User $teacher,
        User $learner,
        string $className = 'Digital Skills',
        string $courseTitle = 'Digital Basics'
    ): array {
        $class = $this->makeClass($teacher, $className);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $suffix = substr(md5($teacher->id.$className.$courseTitle), 0, 8);
        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => $courseTitle.' Subject',
            'slug' => Str::slug($courseTitle).'-subject-'.$suffix,
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => $courseTitle,
            'slug' => Str::slug($courseTitle).'-'.$suffix,
            'level' => 'Beginner',
            'is_active' => true,
        ]);

        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Core Unit',
            'position' => 1,
            'is_active' => true,
        ]);

        $lesson = $this->makeLesson($unit, 'Introduction Lesson', 1);

        return [$class, $course, $unit, $lesson];
    }

    private function makeClass(User $teacher, string $name): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'code' => 'SG-'.strtoupper(substr(md5($name.$teacher->id), 0, 6)),
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
    }

    private function makeLesson(CourseUnit $unit, string $title, int $position): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => $title.' summary',
            'notes' => $title.' notes',
            'estimated_minutes' => 10,
            'position' => $position,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
