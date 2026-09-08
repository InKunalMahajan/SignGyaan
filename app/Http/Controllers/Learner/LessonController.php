<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function show(
        Request $request,
        LearningClass $class,
        Course $course,
        Lesson $lesson
    ): View {
        $this->authorizeLessonAccess($request, $class, $course, $lesson);

        $progress = LessonProgress::firstOrNew([
            'learner_id' => $request->user()->id,
            'lesson_id' => $lesson->id,
        ]);

        if (! $progress->exists) {
            $progress->status = 'in_progress';
            $progress->started_at = now();
        }

        $progress->last_viewed_at = now();
        $progress->save();

        $lessons = $this->publishedLessonsForCourse($course);
        $lessonIds = $lessons->pluck('id');
        $currentIndex = $lessons->search(fn (Lesson $item) => $item->id === $lesson->id);

        $completedCount = $request->user()->lessonProgress()
            ->whereIn('lesson_id', $lessonIds)
            ->where('status', 'completed')
            ->count();
        $totalLessons = $lessons->count();

        return view('learner.classes.lesson', [
            'class' => $class->loadMissing('teacher.teacherProfile'),
            'course' => $course->loadMissing('subject'),
            'lesson' => $lesson->loadMissing('unit'),
            'progress' => $progress,
            'previousLesson' => $currentIndex !== false && $currentIndex > 0 ? $lessons[$currentIndex - 1] : null,
            'nextLesson' => $currentIndex !== false && $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null,
            'completedCount' => $completedCount,
            'totalLessons' => $totalLessons,
            'progressPercent' => $totalLessons > 0 ? (int) round(($completedCount / $totalLessons) * 100) : 0,
        ]);
    }

    public function updateProgress(
        Request $request,
        LearningClass $class,
        Course $course,
        Lesson $lesson
    ): RedirectResponse {
        $this->authorizeLessonAccess($request, $class, $course, $lesson);

        $validated = $request->validate([
            'status' => ['required', Rule::in(LessonProgress::STATUSES)],
        ]);

        $progress = LessonProgress::firstOrNew([
            'learner_id' => $request->user()->id,
            'lesson_id' => $lesson->id,
        ]);

        if (! $progress->started_at) {
            $progress->started_at = now();
        }

        $progress->status = $validated['status'];
        $progress->last_viewed_at = now();
        $progress->completed_at = $validated['status'] === 'completed' ? now() : null;
        $progress->save();

        return back()->with(
            'status',
            $progress->isCompleted() ? 'Lesson marked complete.' : 'Lesson moved back to in progress.'
        );
    }

    private function authorizeLessonAccess(
        Request $request,
        LearningClass $class,
        Course $course,
        Lesson $lesson
    ): void {
        abort_unless(
            $request->user()->enrolledClasses()->whereKey($class->id)->exists(),
            403
        );

        abort_unless(
            $class->courses()
                ->whereKey($course->id)
                ->where('courses.is_active', true)
                ->whereHas('subject', fn ($query) => $query->where('is_active', true))
                ->exists(),
            403
        );

        $lesson->loadMissing('unit');

        abort_unless(
            $lesson->unit?->course_id === $course->id
                && $lesson->unit->is_active
                && $lesson->isPublished(),
            404
        );
    }

    private function publishedLessonsForCourse(Course $course)
    {
        return Lesson::query()
            ->select('lessons.*')
            ->join('course_units', 'course_units.id', '=', 'lessons.course_unit_id')
            ->where('course_units.course_id', $course->id)
            ->where('course_units.is_active', true)
            ->where('lessons.status', 'published')
            ->orderBy('course_units.position')
            ->orderBy('course_units.id')
            ->orderBy('lessons.position')
            ->orderBy('lessons.id')
            ->get();
    }
}
