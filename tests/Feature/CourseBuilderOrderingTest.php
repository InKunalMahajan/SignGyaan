<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Models\VocabularyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBuilderOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reorder_units_lessons_practice_and_vocabulary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create(['name' => 'Digital Skills', 'slug' => 'digital-skills', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Computer Basics', 'slug' => 'computer-basics', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => true]);

        $unitA = Unit::create(['course_id' => $course->id, 'title' => 'Unit A', 'slug' => 'unit-a', 'sort_order' => 1, 'is_published' => true]);
        $unitB = Unit::create(['course_id' => $course->id, 'title' => 'Unit B', 'slug' => 'unit-b', 'sort_order' => 2, 'is_published' => true]);
        $lessonA = Lesson::create(['unit_id' => $unitA->id, 'title' => 'Lesson A', 'slug' => 'lesson-a', 'sort_order' => 1, 'is_published' => true]);
        $lessonB = Lesson::create(['unit_id' => $unitA->id, 'title' => 'Lesson B', 'slug' => 'lesson-b', 'sort_order' => 2, 'is_published' => true]);
        $practiceA = PracticeResource::create(['lesson_id' => $lessonA->id, 'title' => 'Practice A', 'slug' => 'practice-a', 'kind' => 'practice', 'resource_type' => 'exercise', 'sort_order' => 1, 'is_published' => true]);
        $practiceB = PracticeResource::create(['lesson_id' => $lessonA->id, 'title' => 'Practice B', 'slug' => 'practice-b', 'kind' => 'resource', 'resource_type' => 'notes', 'sort_order' => 2, 'is_published' => true]);
        $termA = VocabularyTerm::create(['subject_id' => $subject->id, 'course_id' => $course->id, 'term' => 'Alpha', 'slug' => 'alpha', 'sort_order' => 1, 'is_published' => true]);
        $termB = VocabularyTerm::create(['subject_id' => $subject->id, 'course_id' => $course->id, 'term' => 'Beta', 'slug' => 'beta', 'sort_order' => 2, 'is_published' => true]);

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'units', 'ids' => [$unitB->id, $unitA->id],
        ])->assertOk()->assertJson(['saved' => true]);

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'lessons', 'parent_id' => $unitA->id, 'ids' => [$lessonB->id, $lessonA->id],
        ])->assertOk();

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'practice', 'parent_id' => $lessonA->id, 'ids' => [$practiceB->id, $practiceA->id],
        ])->assertOk();

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'vocabulary', 'ids' => [$termB->id, $termA->id],
        ])->assertOk();

        $this->assertSame(1, $unitB->fresh()->sort_order);
        $this->assertSame(2, $unitA->fresh()->sort_order);
        $this->assertSame(1, $lessonB->fresh()->sort_order);
        $this->assertSame(2, $lessonA->fresh()->sort_order);
        $this->assertSame(1, $practiceB->fresh()->sort_order);
        $this->assertSame(2, $practiceA->fresh()->sort_order);
        $this->assertSame(1, $termB->fresh()->sort_order);
        $this->assertSame(2, $termA->fresh()->sort_order);
    }

    public function test_reorder_rejects_items_outside_the_course_or_incomplete_lists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create(['name' => 'English', 'slug' => 'english', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Course One', 'slug' => 'course-one', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => true]);
        $otherCourse = Course::create(['subject_id' => $subject->id, 'title' => 'Course Two', 'slug' => 'course-two', 'level' => 'Beginner', 'sort_order' => 2, 'is_published' => true]);
        $unitA = Unit::create(['course_id' => $course->id, 'title' => 'A', 'slug' => 'a', 'sort_order' => 1, 'is_published' => true]);
        $unitB = Unit::create(['course_id' => $course->id, 'title' => 'B', 'slug' => 'b', 'sort_order' => 2, 'is_published' => true]);
        $foreignUnit = Unit::create(['course_id' => $otherCourse->id, 'title' => 'Foreign', 'slug' => 'foreign', 'sort_order' => 1, 'is_published' => true]);

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'units', 'ids' => [$unitA->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('ids');

        $this->actingAs($admin)->postJson(route('admin.courses.builder.reorder', $course), [
            'type' => 'units', 'ids' => [$unitA->id, $foreignUnit->id],
        ])->assertUnprocessable()->assertJsonValidationErrors('ids');

        $this->assertSame(1, $unitA->fresh()->sort_order);
        $this->assertSame(2, $unitB->fresh()->sort_order);
    }

    public function test_builder_shows_drag_handles_and_keyboard_order_controls(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create(['name' => 'Science', 'slug' => 'science', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'Science Basics', 'slug' => 'science-basics', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => false]);
        Unit::create(['course_id' => $course->id, 'title' => 'First Unit', 'slug' => 'first-unit', 'sort_order' => 1, 'is_published' => false]);

        $this->actingAs($admin)
            ->get(route('admin.courses.builder', $course))
            ->assertOk()
            ->assertSee('Drag & drop ordering')
            ->assertSee('data-sortable-list', false)
            ->assertSee('data-drag-handle', false)
            ->assertSee('data-move="up"', false)
            ->assertSee('data-move="down"', false)
            ->assertSee(route('admin.courses.builder.reorder', $course), false);
    }
}
