<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassCourseLearnerViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_and_assign_a_new_course_to_owned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($teacher);

        $response = $this->actingAs($teacher)->post(route('teacher.classes.courses.store-new', $class), [
            'subject_name' => 'Information Technology',
            'course_title' => 'Digital Basics',
            'course_level' => 'Beginner',
            'course_description' => 'Learn core digital concepts.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subjects', ['name' => 'Information Technology']);
        $this->assertDatabaseHas('courses', ['title' => 'Digital Basics', 'is_active' => true]);

        $course = Course::where('title', 'Digital Basics')->firstOrFail();
        $this->assertDatabaseHas('class_course_assignments', [
            'learning_class_id' => $class->id,
            'course_id' => $course->id,
            'assigned_by' => $teacher->id,
        ]);
    }

    public function test_teacher_can_assign_an_existing_active_course(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Web Design');

        $response = $this->actingAs($teacher)->post(route('teacher.classes.courses.store', $class), [
            'course_id' => $course->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_course_assignments', [
            'learning_class_id' => $class->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_duplicate_course_assignment_is_rejected(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Digital Marketing');

        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($teacher)
            ->from(route('teacher.classes.show', $class))
            ->post(route('teacher.classes.courses.store', $class), [
                'course_id' => $course->id,
            ]);

        $response->assertRedirect(route('teacher.classes.show', $class));
        $response->assertSessionHasErrors('course_id');
        $this->assertDatabaseCount('class_course_assignments', 1);
    }

    public function test_teacher_cannot_manage_courses_on_another_teachers_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($otherTeacher);
        $course = $this->makeCourse($teacher, 'Computer Skills');

        $this->actingAs($teacher)
            ->post(route('teacher.classes.courses.store', $class), ['course_id' => $course->id])
            ->assertForbidden();
    }

    public function test_archived_class_rejects_new_course_assignments(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($teacher, false);
        $course = $this->makeCourse($teacher, 'English Communication');

        $response = $this->actingAs($teacher)
            ->from(route('teacher.classes.show', $class))
            ->post(route('teacher.classes.courses.store', $class), ['course_id' => $course->id]);

        $response->assertRedirect(route('teacher.classes.show', $class));
        $response->assertSessionHasErrors('course_id');
        $this->assertDatabaseMissing('class_course_assignments', [
            'learning_class_id' => $class->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_teacher_can_remove_a_course_assignment_from_owned_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Storage Devices');
        $class->courses()->attach($course->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $this->actingAs($teacher)
            ->delete(route('teacher.classes.courses.destroy', [$class, $course]))
            ->assertRedirect();

        $this->assertDatabaseMissing('class_course_assignments', [
            'learning_class_id' => $class->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_learner_sees_only_classes_they_are_enrolled_in(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $myClass = $this->makeClass($teacher, true, 'FYJC IT');
        $otherClass = $this->makeClass($teacher, true, 'SYJC IT');
        $myClass->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $response = $this->actingAs($learner)->get(route('learner.classes.index'));

        $response->assertOk();
        $response->assertSee('FYJC IT');
        $response->assertDontSee('SYJC IT');
    }

    public function test_learner_can_open_enrolled_class_and_see_only_active_assigned_courses(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $visibleCourse = $this->makeCourse($teacher, 'Visible Course');
        $inactiveCourse = $this->makeCourse($teacher, 'Inactive Course', false);
        $unassignedCourse = $this->makeCourse($teacher, 'Unassigned Course');

        $class->courses()->attach($visibleCourse->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);
        $class->courses()->attach($inactiveCourse->id, ['assigned_by' => $teacher->id, 'assigned_at' => now()]);

        $response = $this->actingAs($learner)->get(route('learner.classes.show', $class));

        $response->assertOk();
        $response->assertSee('Visible Course');
        $response->assertDontSee('Inactive Course');
        $response->assertDontSee($unassignedCourse->title);
    }

    public function test_learner_cannot_open_a_class_they_are_not_enrolled_in(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertForbidden();
    }

    public function test_non_teacher_cannot_use_teacher_course_assignment_routes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = $this->makeClass($teacher);
        $course = $this->makeCourse($teacher, 'Protected Course');

        $this->actingAs($learner)
            ->post(route('teacher.classes.courses.store', $class), ['course_id' => $course->id])
            ->assertForbidden();
    }

    private function makeClass(User $teacher, bool $active = true, string $name = 'Digital Skills'): LearningClass
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

    private function makeCourse(User $creator, string $title, bool $active = true): Course
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
            'slug' => 'course-'.strtolower(substr(md5($title), 0, 12)),
            'level' => 'Beginner',
            'description' => $title.' description',
            'is_active' => $active,
        ]);
    }
}
