<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewCommandCentreController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pending = Lesson::query()
            ->where('status', 'published')
            ->where('review_status', 'pending')
            ->with(['unit.course.subject', 'unit.course.creator'])
            ->orderByRaw('CASE WHEN teacher_response IS NOT NULL THEN 0 ELSE 1 END')
            ->orderByDesc('review_submitted_at')
            ->orderByDesc('updated_at')
            ->take(20)
            ->get();

        $recentDecisions = Lesson::query()
            ->whereIn('review_status', ['approved', 'changes_requested'])
            ->whereNotNull('reviewed_at')
            ->with(['unit.course.subject', 'unit.course.creator', 'reviewer'])
            ->orderByDesc('reviewed_at')
            ->take(10)
            ->get();

        $notifications = $request->user()->notifications()
            ->latest()
            ->take(8)
            ->get();

        return view('admin.review-command-centre', [
            'pendingLessons' => $pending,
            'recentDecisions' => $recentDecisions,
            'notifications' => $notifications,
            'unreadCount' => $request->user()->unreadNotifications()->count(),
            'pendingCount' => Lesson::where('status', 'published')->where('review_status', 'pending')->count(),
            'resubmittedCount' => Lesson::where('status', 'published')->where('review_status', 'pending')->whereNotNull('teacher_response')->count(),
            'changesRequestedCount' => Lesson::where('status', 'published')->where('review_status', 'changes_requested')->count(),
            'approvedCount' => Lesson::where('status', 'published')->where('review_status', 'approved')->count(),
        ]);
    }
}
