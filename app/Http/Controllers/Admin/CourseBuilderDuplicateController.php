<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Unit;
use App\Services\Admin\CourseContentDuplicator;
use Illuminate\Http\RedirectResponse;

class CourseBuilderDuplicateController extends Controller
{
    public function course(Course $course, CourseContentDuplicator $duplicator): RedirectResponse
    {
        $copy = $duplicator->duplicateCourse($course);

        return redirect()
            ->route('admin.courses.builder', $copy)
            ->with('status', 'Course copied as a draft. Review the copied structure before publishing.');
    }

    public function unit(Course $course, Unit $unit, CourseContentDuplicator $duplicator): RedirectResponse
    {
        abort_unless((int) $unit->course_id === (int) $course->id, 404);

        $copy = $duplicator->duplicateUnit($unit, $course);

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-unit-'.$copy->id)
            ->with('status', 'Unit copied as a draft, including its lessons and learning content.');
    }

    public function lesson(Course $course, Lesson $lesson, CourseContentDuplicator $duplicator): RedirectResponse
    {
        $lesson->loadMissing('unit');
        abort_unless((int) $lesson->unit?->course_id === (int) $course->id, 404);

        $copy = $duplicator->duplicateLesson($lesson, $lesson->unit);

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-lesson-'.$copy->id)
            ->with('status', 'Lesson copied as a draft, including rich content, activities and assessments.');
    }
}
