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

class LearnerContinueLearningTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_resumes_the_saved_current_lesson(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        [$subject, $course, $unit, $lessonOne, $lessonTwo] = $this->publishedCourse();

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonTwo->id,
            'completed_lessons' => ['lesson-'.$lessonOne->id],
            'last_accessed_at' => now(),
        ]);

        $resumeUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lessonTwo->id,
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Resume lesson')
            ->assertSee('Computer Parts')
            ->assertSee('Getting Started')
            ->assertSee('12 min lesson')
            ->assertSee('1 of 2 lessons completed')
            ->assertSee('Continue Lesson')
            ->assertSee($resumeUrl, false);
    }

    public function test_continue_learning_shows_saved_video_watch_percentage(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        [$subject, $course, , $lessonOne] = $this->publishedCourse();

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonOne->id,
            'completed_lessons' => [],
            'video_progress' => [
                'lesson-'.$lessonOne->id => [
                    'position_seconds' => 90,
                    'duration_seconds' => 300,
                    'watched_percent' => 30,
                    'updated_at' => now()->toIso8601String(),
                ],
            ],
            'last_accessed_at' => now(),
        ]);

        $this->actingAs($learner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Current lesson video: 30% watched');
    }

    public function test_most_recent_active_course_is_the_primary_continue_learning_item(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);
        [$subject, $course, , $lessonOne] = $this->publishedCourse();

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subject->slug,
            'subject_name' => $subject->name,
            'course_slug' => $course->slug,
            'course_title' => $course->title,
            'total_lessons' => 2,
            'current_lesson_key' => 'lesson-'.$lessonOne->id,
            'completed_lessons' => [],
            'last_accessed_at' => now()->subDay(),
        ]);

        $subjectTwo = Subject::create([
            'name' => 'Communication',
            'slug' => 'communication',
            'sort_order' => 2,
            'is_published' => true,
        ]);
        $courseTwo = Course::create([
            'subject_id' => $subjectTwo->id,
            'title' => 'Everyday Communication',
            'slug' => 'everyday-communication',
            'level' => 'Beginner',
            'short_description' => 'Build practical communication skills.',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $unitTwo = Unit::create([
            'course_id' => $courseTwo->id,
            'title' => 'Introductions',
            'slug' => 'introductions',
            'sort_order' => 1,
            'is_published' => true,
        ]);
        $lessonTwo = Lesson::create([
            'unit_id' => $unitTwo->id,
            'title' => 'Introducing Yourself',
            'slug' => 'introducing-yourself',
            'content' => 'Introduction practice.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        LearningProgress::create([
            'user_id' => $learner->id,
            'subject_slug' => $subjectTwo->slug,
            'subject_name' => $subjectTwo->name,
            'course_slug' => $courseTwo->slug,
            'course_title' => $courseTwo->title,
            'total_lessons' => 1,
            'current_lesson_key' => 'lesson-'.$lessonTwo->id,
            'completed_lessons' => [],
            'last_accessed_at' => now(),
        ]);

        $response = $this->actingAs($learner)->get(route('dashboard'));

        $response->assertOk()->assertSee('Everyday Communication');
        $this->assertLessThan(
            strpos($response->getContent(), 'Computer Basics'),
            strpos($response->getContent(), 'Everyday Communication')
        );
    }

    private function publishedCourse(): array
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
            'short_description' => 'Learn essential computer skills.',
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

        $lessonOne = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'What is a Computer?',
            'slug' => 'what-is-a-computer',
            'content' => 'Introduction to computers.',
            'estimated_duration_minutes' => 8,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lessonTwo = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Computer Parts',
            'slug' => 'computer-parts',
            'content' => 'Learn the main computer parts.',
            'estimated_duration_minutes' => 12,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lessonOne, $lessonTwo];
    }
}
