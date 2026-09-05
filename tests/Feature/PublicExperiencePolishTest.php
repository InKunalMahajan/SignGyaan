<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicExperiencePolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_layout_exposes_skip_link_and_main_landmark(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="#main-content"', false)
            ->assertSee('id="main-content"', false)
            ->assertSee('data-public-shell', false)
            ->assertSee('data-public-main', false);
    }

    public function test_curriculum_and_lesson_viewer_use_stable_database_lesson_navigation(): void
    {
        [$subject, $course, $unit, $lesson] = $this->createPublishedHierarchy();

        $courseUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
        ]);

        $this->get($courseUrl)
            ->assertOk()
            ->assertSee($unit->title)
            ->assertSee($lesson->title)
            ->assertSee('lesson-'.$lesson->id)
            ->assertDontSee('unit-1-lesson-1');

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]))
            ->assertOk()
            ->assertSee($lesson->title)
            ->assertSee('ISL video not added yet')
            ->assertSee('Sign In to Save Progress');
    }

    private function createPublishedHierarchy(): array
    {
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
            'title' => 'Computer Foundations',
            'slug' => 'computer-foundations',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction to Computers',
            'slug' => 'introduction-to-computers',
            'short_description' => 'A visual introduction to computer basics.',
            'content' => 'Learn the basic ideas of computers.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lesson];
    }
}
