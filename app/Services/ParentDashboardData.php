<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\CourseMastery;
use App\Models\ParentLearnerLink;
use App\Models\User;
use Illuminate\Support\Collection;

class ParentDashboardData
{
    public function forUser(User $parent): array
    {
        $links = ParentLearnerLink::query()
            ->where('parent_user_id', $parent->id)
            ->with(['learner.learnerProfile'])
            ->latest('updated_at')
            ->get();

        $approvedLinks = $links
            ->filter(fn (ParentLearnerLink $link): bool => $link->isApproved() && $link->learner?->is_active)
            ->values();

        $learners = $approvedLinks
            ->map(fn (ParentLearnerLink $link): array => $this->learnerSummary($link))
            ->values();

        $allMasteries = $learners->flatMap(fn (array $learner): Collection => collect($learner['masteries']));
        $allAssessments = $learners->flatMap(fn (array $learner): Collection => collect($learner['assessments']));

        $completedAssessments = $allAssessments->where('status', 'completed');
        $assessmentAverage = $completedAssessments->isEmpty()
            ? null
            : round((float) $completedAssessments->avg('percentage'), 1);

        $supportItems = $learners
            ->flatMap(function (array $learner): Collection {
                return collect($learner['masteries'])
                    ->filter(fn (array $mastery): bool => in_array($mastery['level'], ['needs_support', 'developing'], true))
                    ->map(fn (array $mastery): array => [
                        'learner_name' => $learner['name'],
                        'learner_id' => $learner['id'],
                        'link_id' => $learner['link_id'],
                        'course_title' => $mastery['course_title'],
                        'score' => $mastery['score'],
                        'level' => $mastery['level'],
                        'level_label' => $mastery['level_label'],
                        'message' => $mastery['level'] === 'needs_support'
                            ? 'This course needs extra support.'
                            : 'This course is still developing.',
                    ]);
            })
            ->sortBy('score')
            ->take(6)
            ->values();

        $recentActivity = $learners
            ->flatMap(fn (array $learner): Collection => collect($learner['recent_activity']))
            ->sortByDesc('timestamp')
            ->take(8)
            ->values();

        return [
            'links' => [
                'approved' => $approvedLinks->count(),
                'pending' => $links->where('status', 'pending')->count(),
                'declined' => $links->where('status', 'declined')->count(),
            ],
            'stats' => [
                'linked_learners' => $approvedLinks->count(),
                'active_classes' => $learners->sum('active_classes'),
                'courses_with_mastery' => $allMasteries->count(),
                'assessment_average' => $assessmentAverage,
                'pending_reviews' => $allAssessments->where('status', 'pending_review')->count(),
                'support_items' => $supportItems->count(),
            ],
            'learners' => $learners,
            'support_items' => $supportItems,
            'recent_activity' => $recentActivity,
        ];
    }

    private function learnerSummary(ParentLearnerLink $link): array
    {
        $learner = $link->learner;

        $classes = $learner->enrolledClasses()
            ->where('learning_classes.is_active', true)
            ->withCount('courses')
            ->orderBy('learning_classes.name')
            ->get();

        $masteries = CourseMastery::query()
            ->where('learner_id', $learner->id)
            ->with('course:id,title,is_active')
            ->latest('last_calculated_at')
            ->get()
            ->filter(fn (CourseMastery $mastery): bool => (bool) $mastery->course?->is_active)
            ->unique('course_id')
            ->map(fn (CourseMastery $mastery): array => [
                'course_id' => $mastery->course_id,
                'course_title' => $mastery->course?->title ?? 'Course',
                'score' => (float) $mastery->mastery_score,
                'level' => $mastery->mastery_level,
                'level_label' => $mastery->levelLabel(),
                'lesson_completion' => (float) $mastery->lesson_completion_percentage,
                'assessment_percentage' => $mastery->assessment_percentage !== null
                    ? (float) $mastery->assessment_percentage
                    : null,
                'evidence_status' => $mastery->evidence_status,
                'calculated_at' => $mastery->last_calculated_at,
            ])
            ->values();

        $attempts = AssessmentAttempt::query()
            ->where('learner_id', $learner->id)
            ->whereIn('status', ['submitted', 'pending_review', 'completed'])
            ->with(['assessment:id,course_id,title,status', 'assessment.course:id,title,is_active'])
            ->latest('submitted_at')
            ->latest('id')
            ->get()
            ->filter(fn (AssessmentAttempt $attempt): bool => $attempt->assessment?->status === 'published'
                && (bool) $attempt->assessment?->course?->is_active)
            ->unique('assessment_id')
            ->map(fn (AssessmentAttempt $attempt): array => [
                'attempt_id' => $attempt->id,
                'assessment_id' => $attempt->assessment_id,
                'title' => $attempt->assessment?->title ?? 'Assessment',
                'course_title' => $attempt->assessment?->course?->title ?? 'Course',
                'status' => $attempt->status,
                'percentage' => $attempt->percentage !== null ? (float) $attempt->percentage : null,
                'passed' => $attempt->passed,
                'submitted_at' => $attempt->submitted_at,
                'reviewed_at' => $attempt->reviewed_at,
            ])
            ->values();

        $recentActivity = collect();

        foreach ($attempts as $attempt) {
            $timestamp = $attempt['reviewed_at'] ?? $attempt['submitted_at'];
            if (! $timestamp) {
                continue;
            }

            $recentActivity->push([
                'learner_name' => $learner->name,
                'link_id' => $link->id,
                'title' => $attempt['title'],
                'meta' => $attempt['status'] === 'pending_review'
                    ? 'Assessment result is waiting for teacher review.'
                    : (($attempt['percentage'] !== null ? $attempt['percentage'].'%' : 'Result').' · '.$attempt['course_title']),
                'timestamp' => $timestamp,
                'type' => 'assessment',
            ]);
        }

        foreach ($masteries->take(3) as $mastery) {
            if (! $mastery['calculated_at']) {
                continue;
            }

            $recentActivity->push([
                'learner_name' => $learner->name,
                'link_id' => $link->id,
                'title' => $mastery['course_title'],
                'meta' => $mastery['level_label'].' mastery · '.$mastery['score'].'%',
                'timestamp' => $mastery['calculated_at'],
                'type' => 'mastery',
            ]);
        }

        return [
            'id' => $learner->id,
            'link_id' => $link->id,
            'name' => $learner->name,
            'email' => $learner->email,
            'relationship' => ucfirst($link->relationship),
            'active_classes' => $classes->count(),
            'class_names' => $classes->pluck('name')->values()->all(),
            'mastery_average' => $masteries->isEmpty() ? null : round((float) $masteries->avg('score'), 1),
            'assessment_average' => $attempts->where('status', 'completed')->isEmpty()
                ? null
                : round((float) $attempts->where('status', 'completed')->avg('percentage'), 1),
            'pending_reviews' => $attempts->where('status', 'pending_review')->count(),
            'support_count' => $masteries->whereIn('level', ['needs_support', 'developing'])->count(),
            'masteries' => $masteries->all(),
            'assessments' => $attempts->all(),
            'recent_activity' => $recentActivity->all(),
        ];
    }
}
