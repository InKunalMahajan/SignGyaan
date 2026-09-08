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
use Tests\TestCase;

class LiveLearnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_dashboard_uses_live_class_course_and_lesson_progress(): void
    {
        [$teacher, $learner, $class, $course] = $this->makeLearningPath();
        $unit = $this->makeUnit($course, 'Digital Foundations');
        $completedLesson = $this->makeLesson($unit, 'Computer Basics', 1);
        $resumeLesson = $this->makeLesson($unit, 'Storage Devices', 2);
        $this->makeLesson($unit, 'Teacher Draft', 3, 'draft');

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $completedLesson->id,
            'status' => 'completed',
            'started_at' => now()->subDay(),
            'last_viewed_at' => now()->subHour(),
            'completed_at' => now()->subHour(),
        ]);
        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $resumeLesson->id,
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(20),
            'last_viewed_at' => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response->assertOk();
        $response->assertViewIs('learner.dashboard');
        $response->assertViewHas('activeClassCount', 1);
        $response->assertViewHas('courseCount', 1);
        $response->assertViewHas('inProgressCourseCount', 1);
        $response->assertViewHas('completedLessonCount', 1);
        $response->assertViewHas('totalLessonCount', 2);
        $response->assertViewHas('overallPercent', 50);
        $response->assertViewHas('continueLearning', fn ($item) => $item['lesson']->is($resumeLesson));
        $response->assertSee('Digital Skills');
        $response->assertSee('Storage Devices');
        $response->assertDontSee('Teacher Draft');
    }

    public function test_resume_uses_the_most_recent_unfinished_lesson(): void
    {
        [, $learner, , $course] = $this->makeLearningPath();
        $unit = $this->makeUnit($course, 'Web Basics');
        $older = $this->makeLesson($unit, 'HTML Basics', 1);
        $newer = $this->makeLesson($unit, 'HTML Forms', 2);

        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $older->id,
            'status' => 'in_progress',
            'started_at' => now()->subDays(2),
            'last_viewed_at' => now()->subHours(2),
        ]);
        LessonProgress::create([
            'learner_id' => $learner->id,
            'lesson_id' => $newer->id,
            'status' => 'in_progress',
            'started_at' => now()->subHour(),
            'last_viewed_at' => now()->subMinute(),
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'))
            ->assertViewHas('continueLearning', fn ($item) => $item['lesson']->is($newer));
    }

    public function test_completed_course_is_counted_and_has_no_resume_when_everything_is_complete(): void
    {
        [, $learner, , $course] = $this->makeLearningPath();
        $unit = $this->makeUnit($course, 'Complete Unit');
        $lessonOne = $this->makeLesson($unit, 'Lesson One', 1);
        $lessonTwo = $this->makeLesson($unit, 'Lesson Two', 2);

        foreach ([$lessonOne, $lessonTwo] as $lesson) {
            LessonProgress::create([
                'learner_id' => $learner->id,
                'lesson_id' => $lesson->id,
                'status' => 'completed',
                'started_at' => now()->subDay(),
                'last_viewed_at' => now()->subHour(),
                'completed_at' => now()->subHour(),
            ]);
        }

        $response = $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response->assertViewHas('completedCourseCount', 1);
        $response->assertViewHas('inProgressCourseCount', 0);
        $response->assertViewHas('overallPercent', 100);
        $response->assertViewHas('continueLearning', null);
        $response->assertSee('All published Lessons complete');
    }

    public function test_shared_course_is_counted_once_across_multiple_enrolled_classes(): void
    {
        [$teacher, $learner, $class, $course] = $this->makeLearningPath();
        $secondClass = $this->makeClass($teacher, 'SYJC IT', true);
        $secondClass->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $secondClass->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $unit = $this->makeUnit($course, 'Shared Unit');
        $this->makeLesson($unit, 'Shared Lesson', 1);

        $response = $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response->assertViewHas('totalClassCount', 2);
        $response->assertViewHas('courseCount', 1);
        $response->assertViewHas('totalLessonCount', 1);
    }

    public function test_hidden_units_and_draft_lessons_do_not_affect_live_progress(): void
    {
        [, $learner, , $course] = $this->makeLearningPath();
        $visibleUnit = $this->makeUnit($course, 'Visible Unit');
        $hiddenUnit = $this->makeUnit($course, 'Hidden Unit', false, 2);
        $this->makeLesson($visibleUnit, 'Visible Lesson', 1);
        $this->makeLesson($visibleUnit, 'Draft Lesson', 2, 'draft');
        $this->makeLesson($hiddenUnit, 'Hidden Lesson', 1);

        $response = $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response->assertViewHas('totalLessonCount', 1);
        $response->assertSee('Visible Lesson');
        $response->assertDontSee('Draft Lesson');
        $response->assertDontSee('Hidden Lesson');
    }

    public function test_learner_without_enrollments_gets_a_safe_empty_dashboard(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $response = $this->actingAs($learner)
            ->get(route('dashboard.role', 'learner'));

        $response->assertOk();
        $response->assertViewIs('learner.dashboard');
        $response->assertViewHas('activeClassCount', 0);
        $response->assertViewHas('courseCount', 0);
        $response->assertViewHas('totalLessonCount', 0);
        $response->assertViewHas('overallPercent', 0);
        $response->assertSee('No assigned Courses yet.');
    }

    public function test_teacher_dashboard_still_uses_the_existing_role_dashboard_view(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('dashboard.role', 'teacher'))
            ->assertOk()
            ->assertViewIs('dashboard');
    }

    private function makeLearningPath(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher, 'FYJC IT', true);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology-'.strtolower(substr(md5((string) $teacher->id), 0, 6)),
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Skills',
            'slug' => 'digital-skills-'.strtolower(substr(md5((string) $learner->id), 0, 6)),
            'level' => 'Beginner',
            'description' => 'Accessible digital skills course.',
            'is_active' => true,
        ]);

        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        return [$teacher, $learner, $class, $course];
    }

    private function makeClass(User $teacher, string $name, bool $active): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => $name,
            'code' => 'SG-'.strtoupper(substr(md5($name.$teacher->id), 0, 6)),
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => $active,
        ]);
    }

    private function makeUnit(Course $course, string $title, bool $active = true, int $position = 1): CourseUnit
    {
        return CourseUnit::create([
            'course_id' => $course->id,
            'title' => $title,
            'description' => $title.' description',
            'position' => $position,
            'is_active' => $active,
        ]);
    }

    private function makeLesson(CourseUnit $unit, string $title, int $position, string $status = 'published'): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => $title.' summary',
            'notes' => $title.' notes',
            'estimated_minutes' => 10,
            'position' => $position,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }
}
