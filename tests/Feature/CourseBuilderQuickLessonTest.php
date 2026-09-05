<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBuilderQuickLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_lesson_directly_from_course_builder(): void
    {
        [$admin, $course, $unit] = $this->courseFixture();

        Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Existing Lesson',
            'slug' => 'existing-lesson',
            'sort_order' => 4,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.courses.builder.quick-lesson', $course), [
            'unit_id' => $unit->id,
            'title' => 'Keyboard Basics',
            'short_description' => 'Learn the main keyboard keys.',
            'estimated_duration_minutes' => 12,
            'is_published' => '1',
        ]);

        $lesson = Lesson::query()->where('title', 'Keyboard Basics')->firstOrFail();

        $this->assertSame($unit->id, $lesson->unit_id);
        $this->assertSame('keyboard-basics', $lesson->slug);
        $this->assertSame(5, $lesson->sort_order);
        $this->assertSame(12, $lesson->estimated_duration_minutes);
        $this->assertTrue($lesson->is_published);

        $response->assertRedirect(route('admin.courses.builder', $course).'#builder-lesson-'.$lesson->id);
        $response->assertSessionHas('status');
    }

    public function test_quick_creator_generates_a_unique_slug_for_duplicate_lesson_titles(): void
    {
        [$admin, $course, $unit] = $this->courseFixture();

        Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Keyboard Basics',
            'slug' => 'keyboard-basics',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $this->actingAs($admin)->post(route('admin.courses.builder.quick-lesson', $course), [
            'unit_id' => $unit->id,
            'title' => 'Keyboard Basics',
        ])->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'unit_id' => $unit->id,
            'title' => 'Keyboard Basics',
            'slug' => 'keyboard-basics-2',
            'sort_order' => 2,
            'is_published' => false,
        ]);
    }

    public function test_quick_creator_rejects_a_unit_from_another_course(): void
    {
        [$admin, $course] = $this->courseFixture();

        $otherSubject = Subject::create([
            'name' => 'English',
            'slug' => 'english',
            'sort_order' => 2,
            'is_published' => true,
        ]);
        $otherCourse = Course::create([
            'subject_id' => $otherSubject->id,
            'title' => 'Everyday English',
            'slug' => 'everyday-english',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $otherUnit = Unit::create([
            'course_id' => $otherCourse->id,
            'title' => 'Greetings',
            'slug' => 'greetings',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.courses.builder', $course))
            ->post(route('admin.courses.builder.quick-lesson', $course), [
                'unit_id' => $otherUnit->id,
                'title' => 'Should Not Be Added',
            ]);

        $response->assertRedirect(route('admin.courses.builder', $course));
        $response->assertSessionHasErrors('unit_id');
        $this->assertDatabaseMissing('lessons', ['title' => 'Should Not Be Added']);
    }

    public function test_builder_displays_quick_lesson_creator_for_each_unit(): void
    {
        [$admin, $course, $unit] = $this->courseFixture();

        $this->actingAs($admin)
            ->get(route('admin.courses.builder', $course))
            ->assertOk()
            ->assertSee('Quick Lesson Creator')
            ->assertSee('Add the next lesson to '.$unit->title)
            ->assertSee(route('admin.courses.builder.quick-lesson', $course), false)
            ->assertSee('Full Lesson Editor');
    }

    private function courseFixture(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'Digital Skills',
            'slug' => 'digital-skills',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Computer Basics',
            'slug' => 'computer-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Computer Hardware',
            'slug' => 'computer-hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$admin, $course, $unit];
    }
}
