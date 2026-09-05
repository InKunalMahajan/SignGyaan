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

class CourseBuilderFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_complete_course_builder_workspace(): void
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
        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Keyboard and Mouse',
            'slug' => 'keyboard-and-mouse',
            'sort_order' => 1,
            'is_published' => false,
        ]);
        PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Input Devices Practice',
            'slug' => 'input-devices-practice',
            'kind' => 'practice',
            'resource_type' => 'exercise',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $term = VocabularyTerm::create([
            'subject_id' => $subject->id,
            'course_id' => $course->id,
            'term' => 'Keyboard',
            'slug' => 'keyboard',
            'meaning' => 'An input device used for typing.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lesson->vocabularyTerms()->attach($term->id, ['sort_order' => 1]);

        $this->actingAs($admin)
            ->get(route('admin.courses.builder', $course))
            ->assertOk()
            ->assertSee('Course Builder')
            ->assertSee('Computer Basics')
            ->assertSee('Computer Hardware')
            ->assertSee('Keyboard and Mouse')
            ->assertSee('Input Devices Practice', false)
            ->assertSee('1 vocabulary')
            ->assertSee('Draft')
            ->assertSee(route('admin.units.create', ['course' => $course->id]), false)
            ->assertSee(route('admin.lessons.create', ['unit' => $unit->id]), false)
            ->assertSee(route('admin.practice.create', ['lesson' => $lesson->id]), false);
    }

    public function test_new_course_redirects_admin_into_builder(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $subject = Subject::create([
            'name' => 'English',
            'slug' => 'english',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'subject_id' => $subject->id,
            'title' => 'Everyday English',
            'level' => 'Beginner',
            'sort_order' => 1,
        ]);

        $course = Course::query()->where('slug', 'everyday-english')->firstOrFail();

        $response->assertRedirect(route('admin.courses.builder', $course));
    }

    public function test_learner_cannot_access_course_builder(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create([
            'name' => 'Science',
            'slug' => 'science',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Science Basics',
            'slug' => 'science-basics',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $response = $this->actingAs($learner)->get(route('admin.courses.builder', $course));

        $this->assertTrue(in_array($response->getStatusCode(), [302, 403], true));
    }
}
