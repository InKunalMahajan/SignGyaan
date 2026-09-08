<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReviewFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'review' => ['nullable', Rule::in(Lesson::REVIEW_STATUSES)],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $baseQuery = Lesson::query()
            ->where('status', 'published')
            ->whereHas('unit.course', fn ($query) => $query->where('created_by', $request->user()->id));

        $lessonsQuery = (clone $baseQuery)
            ->with(['unit.course.subject', 'reviewer']);

        if (! empty($validated['review'])) {
            $lessonsQuery->where('review_status', $validated['review']);
        }

        if (! empty($validated['q'])) {
            $term = $validated['q'];
            $lessonsQuery->where(function ($query) use ($term) {
                $query
                    ->where('title', 'like', "%{$term}%")
                    ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('title', 'like', "%{$term}%"))
                    ->orWhereHas('unit.course', fn ($courseQuery) => $courseQuery->where('title', 'like', "%{$term}%"));
            });
        }

        $lessons = $lessonsQuery
            ->orderByRaw("CASE review_status WHEN 'changes_requested' THEN 0 WHEN 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('reviewed_at')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('teacher.reviews.index', [
            'lessons' => $lessons,
            'pendingCount' => (clone $baseQuery)->where('review_status', 'pending')->count(),
            'approvedCount' => (clone $baseQuery)->where('review_status', 'approved')->count(),
            'changesRequestedCount' => (clone $baseQuery)->where('review_status', 'changes_requested')->count(),
        ]);
    }

    public function resubmit(Request $request, Lesson $lesson): RedirectResponse
    {
        $lesson->loadMissing('unit.course');
        abort_unless($lesson->unit?->course?->created_by === $request->user()->id, 403);

        if (! $lesson->changesRequested()) {
            throw ValidationException::withMessages([
                'teacher_response' => 'Only a Lesson with Changes Requested can be resubmitted.',
            ]);
        }

        if (! $lesson->isPublished()) {
            throw ValidationException::withMessages([
                'teacher_response' => 'Publish the Lesson before resubmitting it for review.',
            ]);
        }

        $validated = $request->validate([
            'teacher_response' => ['nullable', 'string', 'max:3000'],
        ]);

        $lesson->update([
            'review_status' => 'pending',
            'review_submitted_at' => now(),
            'teacher_response' => $validated['teacher_response'] ?? null,
        ]);

        return redirect()
            ->route('teacher.reviews.index', ['review' => 'pending'])
            ->with('status', 'Lesson resubmitted for Admin review.');
    }
}
