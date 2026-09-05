<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AcademicProfileService;
use App\Services\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnerController extends Controller
{
    public function index(Request $request, AcademicProfileService $academicProfile, UserManagementService $userManagement): View
    {
        $query = User::query()
            ->where('role', User::ROLE_LEARNER)
            ->withCount(['learningProgress', 'assessmentAttempts', 'learningActivities'])
            ->withMax('learningProgress', 'last_accessed_at');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $status = (string) $request->input('status', '');
        if (array_key_exists($status, $userManagement->statuses())) {
            $query->where('status', $status);
        }

        $board = (string) $request->input('board', '');
        if (array_key_exists($board, $academicProfile->boards())) {
            $query->where('education_board', $board);
        }

        $standard = (string) $request->input('standard', '');
        if (array_key_exists($standard, $academicProfile->standards())) {
            $query->where('standard', $standard);
        }

        $sort = (string) $request->input('sort', 'newest');
        match ($sort) {
            'name' => $query->orderBy('name'),
            'activity' => $query->orderByRaw('learning_progress_max_last_accessed_at IS NULL, learning_progress_max_last_accessed_at DESC'),
            default => $query->latest('created_at'),
        };

        return view('admin.learners.index', [
            'learners' => $query->paginate(20)->withQueryString(),
            'statusOptions' => $userManagement->statuses(),
            'boardOptions' => $academicProfile->boards(),
            'standardOptions' => $academicProfile->standards(),
            'totalLearners' => User::query()->where('role', User::ROLE_LEARNER)->count(),
            'activeLearners' => User::query()->where('role', User::ROLE_LEARNER)->where('status', User::STATUS_ACTIVE)->count(),
            'learnersWithProgress' => User::query()->where('role', User::ROLE_LEARNER)->has('learningProgress')->count(),
            'learnersWithAcademicProfile' => User::query()->where('role', User::ROLE_LEARNER)
                ->whereNotNull('education_board')->whereNotNull('standard')->whereNotNull('academic_year')->count(),
        ]);
    }

    public function show(User $learner, AcademicProfileService $academicProfile, UserManagementService $userManagement): View
    {
        abort_unless($learner->isLearner(), 404);

        $learner->load([
            'learningProgress' => fn ($query) => $query->orderByDesc('last_accessed_at'),
            'assessmentAttempts' => fn ($query) => $query->with('assessment')->orderByDesc('submitted_at')->limit(10),
            'learningActivities' => fn ($query) => $query->orderByDesc('occurred_at')->limit(10),
        ]);

        $completedLessons = $learner->learningProgress
            ->sum(fn ($progress) => $progress->completedLessonsCount());
        $submittedAttempts = $learner->assessmentAttempts->filter(fn ($attempt) => $attempt->isSubmitted());

        return view('admin.learners.show', [
            'learner' => $learner,
            'statusOptions' => $userManagement->statuses(),
            'boardOptions' => $academicProfile->boards(),
            'standardOptions' => $academicProfile->standards(),
            'completedLessons' => $completedLessons,
            'completedCourses' => $learner->learningProgress->whereNotNull('completed_at')->count(),
            'submittedAssessments' => $submittedAttempts->count(),
            'passedAssessments' => $submittedAttempts->where('passed', true)->count(),
            'averageAssessment' => $submittedAttempts->isNotEmpty()
                ? round((float) $submittedAttempts->avg('percentage'), 1)
                : null,
        ]);
    }
}
