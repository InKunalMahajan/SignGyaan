<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use App\Services\LearnerLearningPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearnerLearningPathRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_learning_path_service_exposes_only_valid_assigned_published_content(): void
    {
        [$learner, $class, $course, $unit, $published] = $this->makePath();

        $draft = Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => 'Draft lesson',
            'position' => 2,
            'status' => 'draft',
        ]);

        $inactiveUnit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Inactive chapter',
            'position' => 2,
            'is_active' => false,
        ]);
        $inactiveLesson = $this->makeLesson($inactiveUnit, 'Hidden lesson', 1);

        $otherSubject = Subject::create([
            'created_by' => $class->teacher_id,
            'name' => 'Other Subject',
            'slug' => 'other-subject',
            'is_active' => true,
        ]);
        $unassignedCourse = Course::create([
            'subject_id' => $otherSubject->id,
            'created_by' => $class->teacher_id,
            'title' => 'Unassigned Course',
            'slug' => 'unassigned-course',
            'is_active' => true,
        ]);
        $unassignedUnit = CourseUnit::create([
            'course_id' => $unassignedCourse->id,
            'title' => 'Unassigned chapter',
            'position' => 1,
            'is_active' => true,
        ]);
        $unassignedLesson = $this->makeLesson($unassignedUnit, 'Unassigned lesson', 1);

        $entries = app(LearnerLearningPath::class)->lessonEntriesFor($learner);

        $this->assertTrue($entries->has($published->id));
        $this->assertFalse($entries->has($draft->id));
        $this->assertFalse($entries->has($inactiveLesson->id));
        $this->assertFalse($entries->has($unassignedLesson->id));
    }

    public function test_inactive_subject_course_is_not_accessible_to_learner(): void
    {
        [$learner, $class, $course] = $this->makePath();
        $course->subject->update(['is_active' => false]);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]))
            ->assertForbidden();
    }

    public function test_full_learner_journey_moves_from_class_to_course_to_lesson_to_next_lesson(): void
    {
        [$learner, $class, $course, $unit, $firstLesson] = $this->makePath();
        $secondLesson = $this->makeLesson($unit, 'Second lesson', 2);

        $this->actingAs($learner)
            ->get(route('learner.classes.show', $class))
            ->assertOk()
            ->assertSee($course->title);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.show', [$class, $course]))
            ->assertOk()
            ->assertSee($firstLesson->title)
            ->assertSee($secondLesson->title);

        $this->actingAs($learner)
            ->get(route('learner.classes.courses.lessons.show', [$class, $course, $firstLesson]))
            ->assertOk()
            ->assertSee($firstLesson->title);

        $this->actingAs($learner)
            ->patch(route('learner.classes.courses.lessons.progress.update', [$class, $course, $firstLesson]), [
                'status' => 'completed',
            ])
            ->assertRedirect(route('learner.classes.courses.lessons.show', [$class, $course, $secondLesson]));
    }

    private function makePath(): array
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);

        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'FYJC IT',
            'code' => 'SG-FYJC-IT',
            'subject' => 'Information Technology',
            'level' => '11',
            'academic_year' => '2026-27',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $subject = Subject::create([
            'created_by' => $teacher->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology-phase4d',
            'is_active' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title' => 'Digital Basics',
            'slug' => 'digital-basics-phase4d',
            'is_active' => true,
        ]);
        $class->courses()->attach($course->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $unit = CourseUnit::create([
            'course_id' => $course->id,
            'title' => 'Computer Basics',
            'position' => 1,
            'is_active' => true,
        ]);

        $lesson = $this->makeLesson($unit, 'Introduction to Computers', 1);

        return [$learner, $class, $course, $unit, $lesson];
    }

    private function makeLesson(CourseUnit $unit, string $title, int $position): Lesson
    {
        return Lesson::create([
            'course_unit_id' => $unit->id,
            'title' => $title,
            'summary' => $title.' summary',
            'notes' => $title.' notes',
            'estimated_minutes' => 10,
            'position' => $position,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
