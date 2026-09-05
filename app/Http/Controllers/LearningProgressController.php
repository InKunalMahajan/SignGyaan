<?php

namespace App\Http\Controllers;

use App\Models\LearningProgress;
use App\Services\LearningProgressCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LearningProgressController extends Controller
{
    public function store(Request $request, LearningProgressCatalog $catalog): RedirectResponse
    {
        $validated = $request->validate([
            'subject_slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
            'course_slug' => ['required', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/'],
            'lesson_id' => ['required', 'integer', 'min:1'],
            'action' => ['required', Rule::in(['save', 'complete'])],
        ]);

        $state = $catalog->resolve($validated['subject_slug'], $validated['course_slug']);

        if (! $state || $state['entries']->isEmpty()) {
            throw ValidationException::withMessages([
                'course_slug' => 'This course is not currently available for saved learning progress.',
            ]);
        }

        $currentIndex = $state['entries']->search(
            fn (array $entry) => (int) $entry['lesson']->id === (int) $validated['lesson_id']
        );

        if ($currentIndex === false) {
            throw ValidationException::withMessages([
                'lesson_id' => 'This lesson is not a published lesson in the selected course.',
            ]);
        }

        $currentEntry = $state['entries']->get($currentIndex);
        $nextEntry = $currentIndex < $state['entries']->count() - 1
            ? $state['entries']->get($currentIndex + 1)
            : null;

        $progress = LearningProgress::firstOrNew([
            'user_id' => $request->user()->id,
            'subject_slug' => $state['subject']->slug,
            'course_slug' => $state['course']->slug,
        ]);

        $completedLessons = collect(
            $catalog->normalizeCompleted($progress->completed_lessons ?? [], $state['entries'])
        );

        if ($validated['action'] === 'complete') {
            $completedLessons->push($currentEntry['stable_key']);
            $completedLessons = $completedLessons->unique()->values();
        }

        $currentLessonKey = $validated['action'] === 'complete' && $nextEntry
            ? $nextEntry['stable_key']
            : $currentEntry['stable_key'];

        $progress->fill([
            'subject_name' => $state['subject']->name,
            'course_title' => $state['course']->title,
            'total_lessons' => $state['entries']->count(),
            'current_lesson_key' => $currentLessonKey,
            'completed_lessons' => $completedLessons->all(),
            'last_accessed_at' => now(),
        ]);

        if ($completedLessons->count() >= $state['entries']->count()) {
            $progress->completed_at = $progress->completed_at ?? now();
        } else {
            $progress->completed_at = null;
        }

        $progress->save();

        if ($validated['action'] === 'complete' && $nextEntry) {
            return redirect()
                ->route('courses.show', [
                    'subject' => $state['subject']->slug,
                    'course' => $state['course']->slug,
                    'lesson' => $nextEntry['stable_key'],
                ])
                ->with('status', 'Progress saved. Lesson completed.');
        }

        if ($validated['action'] === 'complete') {
            return redirect()
                ->route('courses.show', [
                    'subject' => $state['subject']->slug,
                    'course' => $state['course']->slug,
                ])
                ->with('status', 'Course progress saved.');
        }

        return back()->with('status', 'Your learning progress has been saved.');
    }
}
