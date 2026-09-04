<?php

namespace App\Http\Controllers;

use App\Models\LearningProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LearningProgressController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
            'subject_name' => ['required', 'string', 'max:120'],
            'course_slug' => ['required', 'string', 'max:140', 'regex:/^[a-z0-9-]+$/'],
            'course_title' => ['required', 'string', 'max:180'],
            'total_lessons' => ['required', 'integer', 'min:1', 'max:500'],
            'lesson_key' => ['required', 'string', 'max:120', 'regex:/^unit-[0-9]+-lesson-[0-9]+$/'],
            'next_lesson_key' => ['nullable', 'string', 'max:120', 'regex:/^unit-[0-9]+-lesson-[0-9]+$/'],
            'action' => ['required', Rule::in(['save', 'complete'])],
        ]);

        $progress = LearningProgress::firstOrNew([
            'user_id' => $request->user()->id,
            'subject_slug' => $validated['subject_slug'],
            'course_slug' => $validated['course_slug'],
        ]);

        $completedLessons = collect($progress->completed_lessons ?? []);

        if ($validated['action'] === 'complete') {
            $completedLessons->push($validated['lesson_key']);
            $completedLessons = $completedLessons->unique()->values();
        }

        $progress->fill([
            'subject_name' => $validated['subject_name'],
            'course_title' => $validated['course_title'],
            'total_lessons' => $validated['total_lessons'],
            'current_lesson_key' => $validated['action'] === 'complete' && ! empty($validated['next_lesson_key'])
                ? $validated['next_lesson_key']
                : $validated['lesson_key'],
            'completed_lessons' => $completedLessons->all(),
            'last_accessed_at' => now(),
        ]);

        if ($completedLessons->count() >= $validated['total_lessons']) {
            $progress->completed_at = $progress->completed_at ?? now();
        } else {
            $progress->completed_at = null;
        }

        $progress->save();

        if ($validated['action'] === 'complete' && ! empty($validated['next_lesson_key'])) {
            return redirect()
                ->route('courses.show', [
                    'subject' => $validated['subject_slug'],
                    'course' => $validated['course_slug'],
                    'lesson' => $validated['next_lesson_key'],
                ])
                ->with('status', 'Progress saved. Lesson completed.');
        }

        if ($validated['action'] === 'complete') {
            return redirect()
                ->route('courses.show', [
                    'subject' => $validated['subject_slug'],
                    'course' => $validated['course_slug'],
                ])
                ->with('status', 'Course progress saved.');
        }

        return back()->with('status', 'Your learning progress has been saved.');
    }
}
