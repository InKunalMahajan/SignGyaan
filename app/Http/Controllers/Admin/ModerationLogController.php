<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewAuditEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModerationLogController extends Controller
{
    private const EVENT_TYPES = [
        'baseline_snapshot',
        'review_submitted',
        'review_resubmitted',
        'review_approved',
        'changes_requested',
        'review_returned_pending',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'event' => ['nullable', Rule::in(self::EVENT_TYPES)],
            'actor_role' => ['nullable', Rule::in(['teacher', 'admin', 'system'])],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $query = ReviewAuditEvent::query()->with('lesson.unit');

        if (! empty($validated['event'])) {
            $query->where('event_type', $validated['event']);
        }

        if (! empty($validated['actor_role'])) {
            $query->where('actor_role', $validated['actor_role']);
        }

        if (! empty($validated['q'])) {
            $term = $validated['q'];
            $query->where(function ($search) use ($term) {
                $search
                    ->where('lesson_title', 'like', "%{$term}%")
                    ->orWhere('course_title', 'like', "%{$term}%")
                    ->orWhere('unit_title', 'like', "%{$term}%")
                    ->orWhere('actor_name', 'like', "%{$term}%")
                    ->orWhere('review_notes', 'like', "%{$term}%")
                    ->orWhere('teacher_response', 'like', "%{$term}%");
            });
        }

        return view('admin.moderation-logs.index', [
            'events' => $query->orderByDesc('occurred_at')->orderByDesc('id')->paginate(30)->withQueryString(),
            'eventTypes' => self::EVENT_TYPES,
            'totalEvents' => ReviewAuditEvent::count(),
            'submissionCount' => ReviewAuditEvent::whereIn('event_type', ['review_submitted', 'review_resubmitted'])->count(),
            'approvalCount' => ReviewAuditEvent::where('event_type', 'review_approved')->count(),
            'changesRequestedCount' => ReviewAuditEvent::where('event_type', 'changes_requested')->count(),
        ]);
    }
}
