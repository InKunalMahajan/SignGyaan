<?php

namespace App\Services;

use App\Models\AcademicClass;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Board;
use App\Models\Course;
use App\Models\CourseMastery;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;

class AdminDashboardData
{
    public function get(): array
    {
        $usersByRole = collect(User::ROLES)->mapWithKeys(fn (string $role): array => [
            $role => [
                'total' => User::query()->where('role', $role)->count(),
                'active' => User::query()->where('role', $role)->where('is_active', true)->count(),
            ],
        ]);

        $activeClasses = LearningClass::query()->where('is_active', true);
        $completedAttempts = AssessmentAttempt::query()->where('status', 'completed');
        $completedAverage = (clone $completedAttempts)->whereNotNull('percentage')->avg('percentage');

        $masteryDistribution = collect(CourseMastery::LEVELS)->mapWithKeys(fn (string $level): array => [
            $level => CourseMastery::query()->where('mastery_level', $level)->count(),
        ]);

        $alerts = $this->alerts();

        return [
            'users' => [
                'total' => User::query()->count(),
                'active' => User::query()->where('is_active', true)->count(),
                'inactive' => User::query()->where('is_active', false)->count(),
                'new_this_month' => User::query()->where('created_at', '>=', now()->startOfMonth())->count(),
                'by_role' => $usersByRole->all(),
            ],
            'curriculum' => [
                'boards' => Board::query()->count(),
                'academic_classes' => AcademicClass::query()->count(),
                'subjects' => Subject::query()->count(),
                'active_subjects' => Subject::query()->where('is_active', true)->count(),
                'courses' => Course::query()->count(),
                'active_courses' => Course::query()->where('is_active', true)->count(),
                'chapters' => CourseUnit::query()->count(),
                'active_chapters' => CourseUnit::query()->where('is_active', true)->count(),
                'lessons' => Lesson::query()->count(),
                'published_lessons' => Lesson::query()->where('status', 'published')->count(),
                'draft_lessons' => Lesson::query()->where('status', 'draft')->count(),
            ],
            'teaching' => [
                'classes' => LearningClass::query()->count(),
                'active_classes' => (clone $activeClasses)->count(),
                'learners_enrolled' => User::query()
                    ->where('role', 'learner')
                    ->whereHas('enrolledClasses', fn ($query) => $query->where('learning_classes.is_active', true))
                    ->count(),
                'class_enrollments' => (clone $activeClasses)->withCount('learners')->get()->sum('learners_count'),
                'course_assignments' => (clone $activeClasses)->withCount('courses')->get()->sum('courses_count'),
                'classes_without_learners' => (clone $activeClasses)->doesntHave('learners')->count(),
                'classes_without_courses' => (clone $activeClasses)->doesntHave('courses')->count(),
            ],
            'assessments' => [
                'total' => Assessment::query()->count(),
                'published' => Assessment::query()->where('status', 'published')->count(),
                'draft' => Assessment::query()->where('status', 'draft')->count(),
                'archived' => Assessment::query()->where('status', 'archived')->count(),
                'attempts' => AssessmentAttempt::query()->count(),
                'pending_review' => AssessmentAttempt::query()->where('status', 'pending_review')->count(),
                'completed' => (clone $completedAttempts)->count(),
                'average_percentage' => $completedAverage !== null ? round((float) $completedAverage, 1) : null,
                'passed' => AssessmentAttempt::query()->where('status', 'completed')->where('passed', true)->count(),
            ],
            'mastery' => [
                'snapshots' => CourseMastery::query()->count(),
                'learners_tracked' => CourseMastery::query()->distinct()->count('learner_id'),
                'courses_tracked' => CourseMastery::query()->distinct()->count('course_id'),
                'average_score' => ($avg = CourseMastery::query()->avg('mastery_score')) !== null ? round((float) $avg, 1) : null,
                'distribution' => $masteryDistribution->all(),
            ],
            'alerts' => $alerts,
            'recent' => $this->recentActivity(),
        ];
    }

    private function alerts(): Collection
    {
        $items = collect();

        $pendingReviews = AssessmentAttempt::query()->where('status', 'pending_review')->count();
        if ($pendingReviews > 0) {
            $items->push(['severity' => 'action', 'title' => 'Assessment reviews pending', 'count' => $pendingReviews, 'route' => 'admin.progress.index']);
        }

        $draftLessons = Lesson::query()->where('status', 'draft')->count();
        if ($draftLessons > 0) {
            $items->push(['severity' => 'attention', 'title' => 'Lesson drafts awaiting completion', 'count' => $draftLessons, 'route' => 'admin.curriculum.content.index']);
        }

        $draftAssessments = Assessment::query()->where('status', 'draft')->count();
        if ($draftAssessments > 0) {
            $items->push(['severity' => 'attention', 'title' => 'Assessment drafts', 'count' => $draftAssessments, 'route' => 'admin.progress.index']);
        }

        $classesWithoutLearners = LearningClass::query()->where('is_active', true)->doesntHave('learners')->count();
        if ($classesWithoutLearners > 0) {
            $items->push(['severity' => 'attention', 'title' => 'Active classes without learners', 'count' => $classesWithoutLearners, 'route' => 'admin.teaching.index']);
        }

        $classesWithoutCourses = LearningClass::query()->where('is_active', true)->doesntHave('courses')->count();
        if ($classesWithoutCourses > 0) {
            $items->push(['severity' => 'attention', 'title' => 'Active classes without courses', 'count' => $classesWithoutCourses, 'route' => 'admin.teaching.index']);
        }

        $supportSnapshots = CourseMastery::query()->where('mastery_level', 'needs_support')->count();
        if ($supportSnapshots > 0) {
            $items->push(['severity' => 'support', 'title' => 'Course mastery snapshots needing support', 'count' => $supportSnapshots, 'route' => 'admin.progress.index']);
        }

        $inactiveUsers = User::query()->where('is_active', false)->count();
        if ($inactiveUsers > 0) {
            $items->push(['severity' => 'info', 'title' => 'Inactive user accounts', 'count' => $inactiveUsers, 'route' => 'admin.users.index']);
        }

        return $items->take(8)->values();
    }

    private function recentActivity(): Collection
    {
        $items = collect();

        User::query()->latest()->limit(4)->get()->each(function (User $user) use ($items): void {
            $items->push([
                'title' => $user->name,
                'meta' => ucfirst($user->role).' account · '.($user->is_active ? 'Active' : 'Inactive'),
                'timestamp' => $user->created_at,
                'route' => 'admin.users.show',
                'route_parameter' => $user->id,
            ]);
        });

        AssessmentAttempt::query()
            ->whereIn('status', ['pending_review', 'completed'])
            ->with(['assessment:id,title'])
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->each(function (AssessmentAttempt $attempt) use ($items): void {
                $items->push([
                    'title' => $attempt->assessment?->title ?? 'Assessment',
                    'meta' => $attempt->status === 'pending_review' ? 'Result pending review' : 'Assessment result completed',
                    'timestamp' => $attempt->updated_at,
                    'route' => 'admin.progress.index',
                    'route_parameter' => null,
                ]);
            });

        return $items->sortByDesc('timestamp')->take(8)->values();
    }
}
