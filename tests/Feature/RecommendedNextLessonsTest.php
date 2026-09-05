<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendedNextLessonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_recommends_the_next_unfinished_published_lesson(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
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
            'short_description' => 'Learn computer basics.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Getting Started',
            'slug' => 'getting-started',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $one = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Introduction',
            'slug' => 'introduction',
            'content' => 'Intro lesson.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $two = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Using a Computer',
            'slug' => 'using-a-computer',
            'content' => 'Current lesson.',
            'sort_order' => 2,
            'is_published' => true,
        ]);
        $three = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Files and Folders',
            'slug' => 'files-and-folders',
            'content' => 'Next lesson.',
            'estimated_duration_minutes' => 15,
            'sort_order' => 3,
            'is_published' => true,
        ]);

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 3,
            'current_lesson_key' => 'lesson-'.$two->id,
            'completed_lessons' => ['lesson-'.$one->id],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Recommended next lessons')
            ->assertSee('Files and Folders')
            ->assertSee('15 min lesson')
            ->assertSee(route('courses.show', [
                'subject' => $subject->slug,
                'course' => $course->slug,
                'lesson' => 'lesson-'.$three->id,
            ]), false);
    }

    public function test_recommendations_ignore_draft_lessons(): void
    {
        $user = User::factory()->create(['role' => 'learner']);
        $subject = Subject::create(['name' => 'IT', 'slug' => 'it', 'sort_order' => 1, 'is_published' => true]);
        $course = Course::create(['subject_id' => $subject->id, 'title' => 'IT Basics', 'slug' => 'it-basics', 'level' => 'Beginner', 'sort_order' => 1, 'is_published' => true]);
        $unit = Unit::create(['course_id' => $course->id, 'title' => 'Unit One', 'slug' => 'unit-one', 'sort_order' => 1, 'is_published' => true]);
        $current = Lesson::create(['unit_id' => $unit->id, 'title' => 'Published Lesson', 'slug' => 'published-lesson', 'content' => 'Published', 'sort_order' => 1, 'is_published' => true]);
        Lesson::create(['unit_id' => $unit->id, 'title' => 'Draft Secret Lesson', 'slug' => 'draft-secret-lesson', 'content' => 'Draft', 'sort_order' => 2, 'is_published' => false]);

        LearningProgress::create([
            'user_id' => $user->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$current->id,
            'completed_lessons' => [],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Draft Secret Lesson');
    }
}
