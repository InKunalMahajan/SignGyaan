<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use App\Models\ParentLearnerLink;
use App\Support\LearnerProgressSummary;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, LearnerProgressSummary $progressSummary): View
    {
        $parent = $request->user();

        $links = ParentLearnerLink::query()
            ->where('parent_user_id', $parent->id)
            ->with(['learner.learnerProfile'])
            ->orderByRaw("FIELD(status, 'approved', 'pending', 'declined')")
            ->orderByDesc('updated_at')
            ->get();

        $approvedLinks = $links->where('status', 'approved')->values();
        $pendingCount = $links->where('status', 'pending')->count();

        $learnerCards = $approvedLinks->map(function (ParentLearnerLink $link) use ($progressSummary) {
            return [
                'link' => $link,
                'learner' => $link->learner,
                'profile' => $link->learner?->learnerProfile,
                'progress' => $progressSummary->build($link->learner),
            ];
        });

        $recentActivity = $learnerCards
            ->flatMap(function (array $card) {
                return $card['progress']['recent_activity']->map(function (array $activity) use ($card) {
                    return [
                        ...$activity,
                        'learner' => $card['learner'],
                        'link' => $card['link'],
                    ];
                });
            })
            ->sortByDesc('last_viewed_at')
            ->take(6)
            ->values();

        $averageProgress = $learnerCards->isNotEmpty()
            ? (int) round($learnerCards->avg(fn ($card) => $card['progress']['overall_percent']))
            : 0;

        return view('parents.dashboard', [
            'parent' => $parent,
            'learnerCards' => $learnerCards,
            'approvedLearnerCount' => $approvedLinks->count(),
            'pendingCount' => $pendingCount,
            'activeClassCount' => $learnerCards->sum(fn ($card) => $card['progress']['active_class_count']),
            'completedLessonCount' => $learnerCards->sum(fn ($card) => $card['progress']['completed_lesson_count']),
            'averageProgress' => $averageProgress,
            'recentActivity' => $recentActivity,
        ]);
    }
}
