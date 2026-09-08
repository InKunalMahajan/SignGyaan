<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningClass;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CourseAssignmentController extends Controller
{
    public function store(Request $request, LearningClass $class): RedirectResponse
    {
        $this->authorizeOwner($request, $class);
        $this->ensureClassIsActive($class);

        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
        ]);

        $course = Course::query()
            ->with('subject')
            ->whereKey($validated['course_id'])
            ->where('is_active', true)
            ->first();

        if (! $course || ! $course->subject?->is_active) {
            throw ValidationException::withMessages([
                'course_id' => 'Choose an active SignGyaan course.',
            ]);
        }

        if ($class->courses()->whereKey($course->id)->exists()) {
            throw ValidationException::withMessages([
                'course_id' => 'This course is already assigned to the class.',
            ]);
        }

        $class->courses()->attach($course->id, [
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]);

        return back()->with('status', "{$course->title} was assigned to the class.");
    }

    public function storeNew(Request $request, LearningClass $class): RedirectResponse
    {
        $this->authorizeOwner($request, $class);
        $this->ensureClassIsActive($class);

        $validated = $request->validate([
            'subject_name' => ['required', 'string', 'max:120'],
            'course_title' => ['required', 'string', 'max:160'],
            'course_level' => ['nullable', 'string', 'max:100'],
            'course_description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subjectSlug = Str::slug($validated['subject_name']);
        if ($subjectSlug === '') {
            $subjectSlug = 'subject-'.Str::lower(Str::random(8));
        }

        $subject = Subject::firstOrCreate(
            ['slug' => $subjectSlug],
            [
                'created_by' => $request->user()->id,
                'name' => $validated['subject_name'],
                'is_active' => true,
            ]
        );

        if (! $subject->is_active) {
            throw ValidationException::withMessages([
                'subject_name' => 'This subject is inactive and cannot receive a new course.',
            ]);
        }

        $course = Course::create([
            'subject_id' => $subject->id,
            'created_by' => $request->user()->id,
            'title' => $validated['course_title'],
            'slug' => $this->uniqueCourseSlug($subject->slug, $validated['course_title']),
            'level' => $validated['course_level'] ?? null,
            'description' => $validated['course_description'] ?? null,
            'is_active' => true,
        ]);

        $class->courses()->attach($course->id, [
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
        ]);

        return back()->with('status', "{$course->title} was created and assigned to the class.");
    }

    public function destroy(Request $request, LearningClass $class, Course $course): RedirectResponse
    {
        $this->authorizeOwner($request, $class);

        abort_unless($class->courses()->whereKey($course->id)->exists(), 404);

        $class->courses()->detach($course->id);

        return back()->with('status', "{$course->title} was removed from the class.");
    }

    private function authorizeOwner(Request $request, LearningClass $class): void
    {
        abort_unless($class->teacher_id === $request->user()->id, 403);
    }

    private function ensureClassIsActive(LearningClass $class): void
    {
        if (! $class->is_active) {
            throw ValidationException::withMessages([
                'course_id' => 'Activate this class before assigning courses.',
            ]);
        }
    }

    private function uniqueCourseSlug(string $subjectSlug, string $title): string
    {
        $base = trim($subjectSlug.'-'.Str::slug($title), '-');
        $base = $base !== '' ? $base : 'course';
        $slug = $base;
        $suffix = 2;

        while (Course::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
