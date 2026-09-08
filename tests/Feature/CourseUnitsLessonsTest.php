<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseUnitsLessonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_sees_only_courses_they_created_in_my_courses(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $mine = $this->makeCourse($teacher, 'My Course');
        $other = $this->makeCourse($otherTeacher, 'Other Course');

        $response = $this->actingAs($teacher)->get(route('teacher.courses.index'));

        $response->assertOk();
        $response->assertSee($mine->title);
        $response->assertDontSee($other->title);
    }

    public function test_course_creator_can_create_and_update_a_unit(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = $this->makeCourse($teacher, 'Digital Foundations');

        $this->actingAs($teacher)
            ->post(route('teacher.courses.units.store', $course), [
                'title' => 'Introduction to Computers',
                'description' => 'Core concepts',
                'position' => 2,
            ])
            ->assertRedirect();

        $unit = CourseUnit::where('course_id', $course->id)->firstOrFail();

        $this->actingAs($teacher)
            ->put(route('teacher.courses.units.update', [$course, $unit]), [
                'title' => 'Computer Fundamentals',
                'description' => 'Updated concepts',
                'position' => 1,
                'is_active' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('course_units', [
            'id' => $unit->id,
            'title' => 'Computer Fundamentals',
            'position' => 1,
            'is_active' => false,
        ]);
    }

    public function test_another_teacher_cannot_manage_course_curriculum(): void
    {
        $creator = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $course = $this->makeCourse($creator, 'Protected Course');

        $this->actingAs($otherTeacher)
            ->get(route('teacher.courses.curriculum.show', $course))
            ->assertForbidden();

        $this->actingAs($otherTeacher)
            ->post(route('teacher.courses.units.store', $course), [
                'title' => 'Unauthorized Unit',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('course_units', ['title' => 'Unauthorized Unit']);
    }

    public function test_teacher_can_create_draft_and_published_lessons(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = $this->makeCourse($teacher, 'Web Design');
        $unit = $this->makeUnit($course, 'HTML Basics');

        $this->actingAs($teacher)
            ->post(route('teacher.courses.units.lessons.store', [$course, $unit]), [
                'title' => 'Introduction to HTML',
                'summary' => 'HTML foundation',
                'isl_video_url' => 'https://example.com/isl/html',
                'notes' => 'HTML stands for HyperText Markup Language.',
                'estimated_minutes' => 15,
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->actingAs($teacher)
            ->post(route('teacher.courses.units.lessons.store', [$course, $unit]), [
                'title' => 'HTML Text Formatting',
                'summary' => 'Formatting tags',
                'estimated_minutes' => 20,
                'status' => 'published',
            ])
            ->assertRedirect();

        $draft = Lesson::where('title', 'Introduction to HTML')->firstOrFail();
        $published = Lesson::where('title', 'HTML Text Formatting')->firstOrFail();

        $this->assertNull($draft->published_at);
        $this->assertNotNull($published->published_at);
        $this->assertSame('draft', $draft->status);
        $this->assertSame('published', $published->status);
    }

    public function test_publishing_and_unpublishing_a_lesson_updates_publish_timestamp(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = $this->makeCourse($teacher, 'Digital Marketing');
        $unit = $this->makeUnit($course, 'SEO');
        $lesson = $this->makeLesson($unit, 'Search Engines', 'draft');

        $this->actingAs($teacher)
            ->put(route('teacher.courses.units.lessons.update', [$course, $unit, $lesson]), [
                'title' => $lesson->title,
                'summary' => null,
                'isl_video_url' => null,
                'notes' => null,
                'estimated_minutes' => 10,
                'position' => 1,
                'status' => 'published',
            ])
            ->assertRedirect();

        $lesson->refresh();
        $this->assertSame('published', $lesson->status);
        $this->assertNotNull($lesson->published_at);

        $this->actingAs($teacher)
            ->put(route('teacher.courses.units.lessons.update', [$course, $unit, $lesson]), [
                'title' => $lesson->title,
                'summary' => null,
                'isl_video_url' => null,
                'notes' => null,
                'estimated_minutes' => 10,
                'position' => 1,
                'status' => 'draft',
            ])
            ->assertRedirect();

        $lesson->refresh();
        $this->assertSame('draft', $lesson->status);
        $this->assertNull($lesson->published_at);
    }

    public function test_deleting_unit_cascades_its_lessons(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $course = $this->makeCourse($teacher, 'Computer Basics');
        $unit = $this->makeUnit($course, 'Hardware');
        $lesson = $this->makeLesson($unit, 'Input Devices', 'published');

        $this->actingAs($teacher)
            ->delete(route('teacher.courses.units.destroy', [$course, $unit]))
            ->assertRedirect();

        $this->assertDatabaseMissing('course_units', ['id' => $unit->id]);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    public function test_learner_sees_only_active_units_and_published_lessons(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Accessible IT');
        $this->enrollAndAssign($class, $learner, $course, $teacher);

        $visibleUnit = $this->makeUnit($course, 'Visible Unit', true, 1);
        $hiddenUnit = $this->makeUnit($course, 'Hidden Unit', false, 2);
        $this->makeLesson($visibleUnit, 'Published Lesson', 'published', 1);
        $this->makeLesson($visibleUnit, 'Draft Lesson', 'draft', 2);
        $this->makeLesson($hiddenUnit, 'Hidden Unit Lesson', 'published', 1);

        $response = $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]));

        $response->assertOk();
        $response->assertSee('Visible Unit');
        $response->assertSee('Published Lesson');
        $response->assertDontSee('Draft Lesson');
        $response->assertDontSee('Hidden Unit');
        $response->assertDontSee('Hidden Unit Lesson');
    }

    public function test_learner_cannot_open_course_not_assigned_to_their_enrolled_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Unassigned Course');
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]))
            ->assertForbidden();
    }

    public function test_learner_cannot_open_course_through_a_class_they_are_not_enrolled_in(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Assigned Course');
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]))
            ->assertForbidden();
    }

    private function makeClass(User $teacher): LearningClass
    {
        return LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC IT',
            'code' => 'SG-'.strtoupper(substr(md5((string) $teacher->id), 0, 6)),
            'subject' => 'Information Technology',
            'level' => 'Beginner',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
    }

    private function makeCourse(User $creator, string $title): Course
    {
        $subject = Subject::firstOrCreate(
            ['slug' => 'information-technology'],
            [
                'created_by' => $creator->id,
                'name' => 'Information Technology',
                'is_active' => true,
            ]
        );

        return Course::create([
            'subject_id' => $subject->id,
            'created_by' => $creator->id,
            'title' => $title,
            'slug' => 'course-'.strtolower(substr(md5($title.$creator->id), 0, 12)),
            'level' => 'Beginner',
            'description' => $title.' description',
            'is_active' => true,
        ]);
    }

    private function makeUnit(Course $course, string $title, bool $active = true, int $position = 1): CourseUnit
    {
        return CourseUnit::create([
            'course_id' => $course->id,
            'title' => $title,
            'position' => $position,
            'is_active' => $active,
        ]);
    }

    private function makeLesson(CourseUnit $unit, string $title, string $status, int $position = 1): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'position' => $position,
            'status' => $status,
            'published_at' => $status === 'published' ? now() : null,
        ]);
    }

    private function enrollAndAssign(
        LearningClass $class,
        User $learner,
        Course $course,
        User $teacher
    ): void {
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);
        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);
    }
}
