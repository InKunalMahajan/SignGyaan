<?php

namespace App\Services;

use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Collection;

class LearnerLearningPath
{
    /**
     * Return active enrolled classes with only learner-accessible curriculum.
     */
    public function activeClassesFor(User $learner): Collection
    {
        return $learner->enrolledClasses()
            ->where('learning_classes.is_active', true)
            ->with([
                'teacher.teacherProfile',
                'courses' => fn ($query) => $this->scopeAccessibleCourses($query)
                    ->with($this->courseCurriculumRelations()),
            ])
            ->orderBy('learning_classes.name')
            ->get();
    }

    /**
     * Load one enrolled class with only accessible assigned curriculum.
     * Archived classes remain reviewable when explicitly opened.
     */
    public function loadClassFor(User $learner, LearningClass $class): LearningClass
    {
        $this->assertEnrolled($learner, $class);

        return $class->load([
            'teacher.teacherProfile',
            'courses' => fn ($query) => $this->scopeAccessibleCourses($query)
                ->with($this->courseCurriculumRelations()),
        ]);
    }

    /**
     * Validate an assigned course and load its active units + published lessons.
     */
    public function loadCourseFor(User $learner, LearningClass $class, Course $course): Course
    {
        $this->assertEnrolled($learner, $class);

        abort_unless(
            $class->courses()
                ->whereKey($course->id)
                ->where('courses.is_active', true)
                ->whereHas('subject', fn ($query) => $query->where('is_active', true))
                ->exists(),
            403
        );

        return $course->load([
            'subject',
            'units' => fn ($query) => $query
                ->where('is_active', true)
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->where('status', 'published')
                        ->orderBy('position')
                        ->orderBy('id'),
                ])
                ->orderBy('position')
                ->orderBy('id'),
        ]);
    }

    /**
     * Flatten the valid published lessons for a course in learning order.
     */
    public function lessonsForCourse(User $learner, LearningClass $class, Course $course): Collection
    {
        $course = $this->loadCourseFor($learner, $class, $course);

        return $course->units
            ->flatMap(fn ($unit) => $unit->lessons)
            ->values();
    }

    /**
     * Validate that a lesson belongs to the learner's accessible course path.
     */
    public function assertLessonAccessible(
        User $learner,
        LearningClass $class,
        Course $course,
        Lesson $lesson
    ): void {
        $lessons = $this->lessonsForCourse($learner, $class, $course);

        abort_unless($lessons->contains(fn (Lesson $item) => $item->id === $lesson->id), 404);
    }

    /**
     * Build a unique map of every currently accessible lesson for Dashboard,
     * Continue Learning, reporting, and future assessment eligibility.
     */
    public function lessonEntriesFor(User $learner): Collection
    {
        return $this->lessonEntriesFromClasses($this->activeClassesFor($learner));
    }

    public function lessonEntriesFromClasses(Collection $classes): Collection
    {
        $entries = collect();

        foreach ($classes as $class) {
            foreach ($class->courses as $course) {
                foreach ($course->units as $unit) {
                    foreach ($unit->lessons as $lesson) {
                        if (! $entries->has($lesson->id)) {
                            $entries->put($lesson->id, [
                                'class' => $class,
                                'course' => $course,
                                'unit' => $unit,
                                'lesson' => $lesson,
                            ]);
                        }
                    }
                }
            }
        }

        return $entries;
    }

    public function assertEnrolled(User $learner, LearningClass $class): void
    {
        abort_unless(
            $learner->enrolledClasses()->whereKey($class->id)->exists(),
            403
        );
    }

    private function scopeAccessibleCourses($query)
    {
        return $query
            ->where('courses.is_active', true)
            ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('is_active', true))
            ->orderBy('title');
    }

    private function courseCurriculumRelations(): array
    {
        return [
            'subject',
            'units' => fn ($unitQuery) => $unitQuery
                ->where('is_active', true)
                ->with([
                    'lessons' => fn ($lessonQuery) => $lessonQuery
                        ->where('status', 'published')
                        ->orderBy('position')
                        ->orderBy('id'),
                ])
                ->orderBy('position')
                ->orderBy('id'),
        ];
    }
}
