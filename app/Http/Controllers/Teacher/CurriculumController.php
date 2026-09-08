<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->where('created_by', $request->user()->id)
            ->with('subject')
            ->withCount('units')
            ->orderBy('title')
            ->paginate(12);

        return view('teacher.courses.index', [
            'courses' => $courses,
        ]);
    }

    public function show(Request $request, Course $course): View
    {
        $this->authorizeCourseOwner($request, $course);

        $course->load([
            'subject',
            'units' => fn ($query) => $query
                ->with(['lessons' => fn ($lessonQuery) => $lessonQuery->orderBy('position')->orderBy('id')])
                ->orderBy('position')
                ->orderBy('id'),
        ]);

        return view('teacher.courses.curriculum', [
            'course' => $course,
            'unitCount' => $course->units->count(),
            'lessonCount' => $course->units->sum(fn ($unit) => $unit->lessons->count()),
            'publishedLessonCount' => $course->units->sum(
                fn ($unit) => $unit->lessons->where('status', 'published')->count()
            ),
            'approvedLessonCount' => $course->units->sum(
                fn ($unit) => $unit->lessons
                    ->where('status', 'published')
                    ->where('review_status', 'approved')
                    ->count()
            ),
            'pendingReviewCount' => $course->units->sum(
                fn ($unit) => $unit->lessons->where('review_status', 'pending')->count()
            ),
        ]);
    }

    public function storeUnit(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeCourseOwner($request, $course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'position' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $position = $validated['position']
            ?? ((int) $course->units()->max('position') + 1);

        $course->units()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'position' => max(1, $position),
            'is_active' => true,
        ]);

        return back()->with('status', 'Unit created successfully.');
    }

    public function updateUnit(Request $request, Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->authorizeCourseOwner($request, $course);
        $this->ensureUnitBelongsToCourse($course, $unit);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'position' => ['required', 'integer', 'min:1', 'max:999'],
            'is_active' => ['required', 'boolean'],
        ]);

        $unit->update($validated);

        return back()->with('status', 'Unit updated.');
    }

    public function destroyUnit(Request $request, Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->authorizeCourseOwner($request, $course);
        $this->ensureUnitBelongsToCourse($course, $unit);

        $unit->delete();

        return back()->with('status', 'Unit and its lessons were removed.');
    }

    public function storeLesson(Request $request, Course $course, CourseUnit $unit): RedirectResponse
    {
        $this->authorizeCourseOwner($request, $course);
        $this->ensureUnitBelongsToCourse($course, $unit);

        $validated = $request->validate($this->lessonRules(false));
        $status = $validated['status'];
        $position = $validated['position']
            ?? ((int) $unit->lessons()->max('position') + 1);

        $unit->lessons()->create([
            ...$validated,
            'position' => max(1, $position),
            'published_at' => $status === 'published' ? now() : null,
            'review_status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
        ]);

        return back()->with(
            'status',
            $status === 'published'
                ? 'Lesson saved and sent for Admin review.'
                : 'Lesson draft created successfully.'
        );
    }

    public function updateLesson(
        Request $request,
        Course $course,
        CourseUnit $unit,
        Lesson $lesson
    ): RedirectResponse {
        $this->authorizeCourseOwner($request, $course);
        $this->ensureUnitBelongsToCourse($course, $unit);
        $this->ensureLessonBelongsToUnit($unit, $lesson);

        $validated = $request->validate($this->lessonRules(true));
        $wasPublished = $lesson->isPublished();
        $willBePublished = $validated['status'] === 'published';

        $lesson->update([
            ...$validated,
            'published_at' => match (true) {
                $willBePublished && ! $wasPublished => now(),
                ! $willBePublished => null,
                default => $lesson->published_at,
            },
            'review_status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
        ]);

        return back()->with(
            'status',
            $willBePublished
                ? 'Lesson updated and sent for Admin review.'
                : 'Lesson draft updated.'
        );
    }

    public function destroyLesson(
        Request $request,
        Course $course,
        CourseUnit $unit,
        Lesson $lesson
    ): RedirectResponse {
        $this->authorizeCourseOwner($request, $course);
        $this->ensureUnitBelongsToCourse($course, $unit);
        $this->ensureLessonBelongsToUnit($unit, $lesson);

        $lesson->delete();

        return back()->with('status', 'Lesson removed.');
    }

    private function authorizeCourseOwner(Request $request, Course $course): void
    {
        abort_unless($course->created_by === $request->user()->id, 403);
    }

    private function ensureUnitBelongsToCourse(Course $course, CourseUnit $unit): void
    {
        abort_unless($unit->course_id === $course->id, 404);
    }

    private function ensureLessonBelongsToUnit(CourseUnit $unit, Lesson $lesson): void
    {
        abort_unless($lesson->course_unit_id === $unit->id, 404);
    }

    private function lessonRules(bool $requiresPosition): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:3000'],
            'isl_video_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string', 'max:20000'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'position' => [$requiresPosition ? 'required' : 'nullable', 'integer', 'min:1', 'max:999'],
            'status' => ['required', Rule::in(Lesson::STATUSES)],
        ];
    }
}
