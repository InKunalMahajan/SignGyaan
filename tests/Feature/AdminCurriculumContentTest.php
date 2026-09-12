<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\Board;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCurriculumContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_open_content_workspace(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.curriculum.content.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_course_unit_and_lesson(): void
    {
        [$admin, $subject] = $this->curriculum();

        $this->actingAs($admin)->post(route('admin.curriculum.content.courses.store'), [
            'subject_id' => $subject->id,
            'title' => 'FYJC Information Technology',
            'level' => 'FYJC',
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $course = Course::where('title', 'FYJC Information Technology')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.curriculum.content.units.store', $course), [
            'title' => 'Chapter 1 - Basics of IT',
            'position' => 1,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $unit = CourseUnit::where('course_id', $course->id)->firstOrFail();

        $this->actingAs($admin)->post(route('admin.curriculum.content.lessons.store', [$course, $unit]), [
            'title' => 'Introduction to IT',
            'summary' => 'Simple introduction',
            'estimated_minutes' => 20,
            'status' => 'published',
        ])->assertSessionHasNoErrors();

        $lesson = Lesson::where('course_unit_id', $unit->id)->firstOrFail();

        $this->assertSame($admin->id, $course->created_by);
        $this->assertSame('fyjc-information-technology', $course->slug);
        $this->assertSame(1, $unit->position);
        $this->assertSame('published', $lesson->status);
        $this->assertNotNull($lesson->published_at);
    }

    public function test_admin_can_update_course_unit_and_lesson(): void
    {
        [$admin, $subject] = $this->curriculum();
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'title' => 'Old Course',
            'slug' => 'old-course',
            'is_active' => true,
        ]);
        $unit = $course->units()->create([
            'title' => 'Old Unit',
            'position' => 1,
            'is_active' => true,
        ]);
        $lesson = $unit->lessons()->create([
            'title' => 'Old Lesson',
            'position' => 1,
            'status' => 'draft',
        ]);

        $this->actingAs($admin)->put(route('admin.curriculum.content.courses.update', $course), [
            'subject_id' => $subject->id,
            'title' => 'Updated Course',
            'level' => 'FYJC',
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(route('admin.curriculum.content.units.update', [$course, $unit]), [
            'title' => 'Updated Unit',
            'position' => 2,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(route('admin.curriculum.content.lessons.update', [$course, $unit, $lesson]), [
            'title' => 'Updated Lesson',
            'position' => 2,
            'status' => 'published',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Updated Course', $course->fresh()->title);
        $this->assertSame('Updated Unit', $unit->fresh()->title);
        $this->assertSame('Updated Lesson', $lesson->fresh()->title);
        $this->assertSame('published', $lesson->fresh()->status);
    }

    public function test_course_with_units_cannot_be_deleted(): void
    {
        [$admin, $subject] = $this->curriculum();
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'title' => 'Course',
            'slug' => 'course',
            'is_active' => true,
        ]);
        $course->units()->create([
            'title' => 'Unit',
            'position' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.curriculum.content.courses.destroy', $course))
            ->assertSessionHasErrors('course');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }

    public function test_unit_with_lessons_cannot_be_deleted(): void
    {
        [$admin, $subject] = $this->curriculum();
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'title' => 'Course',
            'slug' => 'course',
            'is_active' => true,
        ]);
        $unit = $course->units()->create([
            'title' => 'Unit',
            'position' => 1,
            'is_active' => true,
        ]);
        $unit->lessons()->create([
            'title' => 'Lesson',
            'position' => 1,
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.curriculum.content.units.destroy', [$course, $unit]))
            ->assertSessionHasErrors('unit');

        $this->assertDatabaseHas('course_units', ['id' => $unit->id]);
    }

    public function test_content_page_renders_full_hierarchy(): void
    {
        [$admin, $subject] = $this->curriculum();
        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'title' => 'FYJC Information Technology',
            'slug' => 'fyjc-information-technology',
            'is_active' => true,
        ]);
        $unit = $course->units()->create([
            'title' => 'Chapter 1 - Basics of IT',
            'position' => 1,
            'is_active' => true,
        ]);
        $unit->lessons()->create([
            'title' => 'Introduction to IT',
            'position' => 1,
            'status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.curriculum.content.index'))
            ->assertOk()
            ->assertSee('Information Technology')
            ->assertSee('FYJC Information Technology')
            ->assertSee('Chapter 1 - Basics of IT')
            ->assertSee('Introduction to IT');
    }

    private function curriculum(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create([
            'name' => 'Maharashtra State Board',
            'slug' => 'maharashtra-state-board',
        ]);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);
        $subject = Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology',
            'is_active' => true,
        ]);

        return [$admin, $subject];
    }
}
