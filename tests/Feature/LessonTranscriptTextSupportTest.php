<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTranscriptTextSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_simplified_summary_transcript_and_vocabulary(): void
    {
        [$unit] = $this->createPublishedCourseTree();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.lessons.store'), [
                'unit_id' => $unit->id,
                'title' => 'Input Devices',
                'slug' => 'input-devices',
                'short_description' => 'Learn common input devices.',
                'simplified_summary' => 'Input devices help us send information to a computer.',
                'isl_transcript' => "A keyboard is used for typing.\nA mouse is used for pointing.",
                'key_vocabulary' => "Keyboard — used to type text\nMouse — pointing device",
                'sort_order' => 1,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.lessons.index'));

        $lesson = Lesson::query()->firstOrFail();

        $this->assertSame('Input devices help us send information to a computer.', $lesson->simplified_summary);
        $this->assertStringContainsString('A keyboard is used for typing.', $lesson->isl_transcript);
        $this->assertStringContainsString('Keyboard — used to type text', $lesson->key_vocabulary);
    }

    public function test_public_lesson_shows_text_alternatives_and_print_action(): void
    {
        [$unit, $subject, $course] = $this->createPublishedCourseTree();

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Input Devices',
            'slug' => 'input-devices',
            'short_description' => 'Learn common input devices.',
            'simplified_summary' => 'Input devices send data to the computer.',
            'isl_transcript' => 'This ISL lesson explains keyboards and mice.',
            'key_vocabulary' => "Keyboard — used to type text\nMouse — pointing device",
            'content' => 'Input devices are hardware used to enter data.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]));

        $response
            ->assertOk()
            ->assertSee('Simple summary')
            ->assertSee('Input devices send data to the computer.')
            ->assertSee('ISL transcript')
            ->assertSee('This ISL lesson explains keyboards and mice.')
            ->assertSee('Key vocabulary')
            ->assertSee('Keyboard')
            ->assertSee('used to type text')
            ->assertSee('Print lesson notes')
            ->assertSee('window.print()', false);
    }

    private function createPublishedCourseTree(): array
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
            'title' => 'Hardware',
            'slug' => 'hardware',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$unit, $subject, $course];
    }
}
