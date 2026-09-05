<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use App\Models\Course;
use App\Models\LearningActivity;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use App\Services\AcademicProfileService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(AcademicProfileService $academicProfileService): View
    {
        $now = now();
        $oneDayAgo = $now->copy()->subDay();
        $sevenDaysAgo = $now->copy()->subDays(7);
        $thirtyDaysAgo = $now->copy()->subDays(30);

        $userStats = [
            'total' => User::query()->count(),
            'learners' => User::query()->where('role', User::ROLE_LEARNER)->count(),
            'teachers' => User::query()->where('role', User::ROLE_TEACHER)->count(),
            'administrators' => User::query()->whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])->count(),
            'active' => User::query()->where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => User::query()->where('status', User::STATUS_SUSPENDED)->count(),
            'disabled' => User::query()->where('status', User::STATUS_DISABLED)->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
            'unverified' => User::query()->whereNull('email_verified_at')->count(),
            'new_7_days' => User::query()->where('created_at', '>=', $sevenDaysAgo)->count(),
            'new_30_days' => User::query()->where('created_at', '>=', $thirtyDaysAgo)->count(),
            'logged_in_30_days' => User::query()->where('last_login_at', '>=', $thirtyDaysAgo)->count(),
        ];

        $userStats['active_rate'] = $userStats['total'] > 0 ? round(($userStats['active'] / $userStats['total']) * 100, 1) : 0.0;
        $userStats['verification_rate'] = $userStats['total'] > 0 ? round(($userStats['verified'] / $userStats['total']) * 100, 1) : 0.0;

        $recentUsers = User::query()->latest('created_at')->limit(6)->get(['id', 'name', 'email', 'role', 'status', 'created_at', 'last_login_at']);

        $contentStats = [
            'subjects' => Subject::query()->count(),
            'courses' => Course::query()->count(),
            'lessons' => Lesson::query()->count(),
            'assessments' => Assessment::query()->count(),
        ];

        $academicStats = [
            'subjects' => Subject::query()->count(),
            'subjects_published' => Subject::query()->where('is_published', true)->count(),
            'subjects_draft' => Subject::query()->where('is_published', false)->count(),
            'courses' => Course::query()->count(),
            'courses_published' => Course::query()->where('is_published', true)->count(),
            'courses_draft' => Course::query()->where('is_published', false)->count(),
            'courses_featured' => Course::query()->where('is_featured', true)->count(),
            'units' => Unit::query()->count(),
            'units_published' => Unit::query()->where('is_published', true)->count(),
            'units_draft' => Unit::query()->where('is_published', false)->count(),
            'lessons' => Lesson::query()->count(),
            'lessons_published' => Lesson::query()->where('is_published', true)->count(),
            'lessons_draft' => Lesson::query()->where('is_published', false)->count(),
            'lessons_with_isl' => Lesson::query()->where(function ($query) {
                $query->whereNotNull('isl_media_asset_id')->orWhereNotNull('isl_video_url');
            })->count(),
            'assessments' => Assessment::query()->count(),
            'assessments_published' => Assessment::query()->where('is_published', true)->count(),
            'assessments_draft' => Assessment::query()->where('is_published', false)->count(),
        ];

        foreach (['subjects', 'courses', 'units', 'lessons', 'assessments'] as $resource) {
            $academicStats[$resource.'_published_rate'] = $academicStats[$resource] > 0
                ? round(($academicStats[$resource.'_published'] / $academicStats[$resource]) * 100, 1)
                : 0.0;
        }

        $academicStats['isl_lesson_rate'] = $academicStats['lessons'] > 0
            ? round(($academicStats['lessons_with_isl'] / $academicStats['lessons']) * 100, 1)
            : 0.0;

        $recentCourses = Course::query()->with('subject:id,name')->latest('updated_at')->limit(5)
            ->get(['id', 'subject_id', 'title', 'slug', 'is_published', 'is_featured', 'updated_at']);

        $progressRecords = LearningProgress::query()->get([
            'user_id', 'course_slug', 'course_title', 'total_lessons', 'completed_lessons', 'last_accessed_at', 'completed_at',
        ]);
        $completedLessons = $progressRecords->sum(fn (LearningProgress $progress) => $progress->completedLessonsCount());
        $totalTrackedLessons = $progressRecords->sum(fn (LearningProgress $progress) => max(0, (int) $progress->total_lessons));

        $learningStats = [
            'tracked_courses' => $progressRecords->count(),
            'completed_courses' => $progressRecords->whereNotNull('completed_at')->count(),
            'completed_lessons' => $completedLessons,
            'tracked_lessons' => $totalTrackedLessons,
            'learners_with_progress' => $progressRecords->pluck('user_id')->filter()->unique()->count(),
            'active_learners_7_days' => LearningActivity::query()->where('occurred_at', '>=', $sevenDaysAgo)->distinct('user_id')->count('user_id'),
            'active_learners_30_days' => LearningActivity::query()->where('occurred_at', '>=', $thirtyDaysAgo)->distinct('user_id')->count('user_id'),
            'activities_7_days' => LearningActivity::query()->where('occurred_at', '>=', $sevenDaysAgo)->count(),
            'activities_30_days' => LearningActivity::query()->where('occurred_at', '>=', $thirtyDaysAgo)->count(),
        ];
        $learningStats['course_completion_rate'] = $learningStats['tracked_courses'] > 0 ? round(($learningStats['completed_courses'] / $learningStats['tracked_courses']) * 100, 1) : 0.0;
        $learningStats['lesson_completion_rate'] = $learningStats['tracked_lessons'] > 0 ? round(($learningStats['completed_lessons'] / $learningStats['tracked_lessons']) * 100, 1) : 0.0;

        $recentLearningActivities = LearningActivity::query()->with('user:id,name,email')->latest('occurred_at')->limit(8)
            ->get(['id', 'user_id', 'activity_type', 'course_slug', 'lesson_id', 'title', 'occurred_at']);
        $recentLearningProgress = LearningProgress::query()->with('user:id,name,email')->whereNotNull('last_accessed_at')->latest('last_accessed_at')->limit(6)->get();

        $submittedAttempts = AssessmentAttempt::query()->where('status', 'submitted');
        $submittedCount = (clone $submittedAttempts)->count();
        $passedCount = (clone $submittedAttempts)->where('passed', true)->count();
        $failedCount = (clone $submittedAttempts)->where('passed', false)->count();
        $assessmentStats = [
            'assessments' => Assessment::query()->count(),
            'published_assessments' => Assessment::query()->where('is_published', true)->count(),
            'total_attempts' => AssessmentAttempt::query()->count(),
            'submitted_attempts' => $submittedCount,
            'in_progress_attempts' => AssessmentAttempt::query()->where('status', 'in-progress')->count(),
            'expired_attempts' => AssessmentAttempt::query()->where('status', 'expired')->count(),
            'passed_attempts' => $passedCount,
            'failed_attempts' => $failedCount,
            'attempts_7_days' => AssessmentAttempt::query()->where('started_at', '>=', $sevenDaysAgo)->count(),
            'attempts_30_days' => AssessmentAttempt::query()->where('started_at', '>=', $thirtyDaysAgo)->count(),
            'participating_learners' => AssessmentAttempt::query()->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'average_percentage' => round((float) ((clone $submittedAttempts)->avg('percentage') ?? 0), 1),
        ];
        $assessmentStats['pass_rate'] = $submittedCount > 0 ? round(($passedCount / $submittedCount) * 100, 1) : 0.0;
        $recentAssessmentAttempts = AssessmentAttempt::query()->with(['user:id,name,email', 'assessment.practiceResource:id,title'])->latest('started_at')->limit(8)->get();

        $teacherQuery = User::query()->where('role', User::ROLE_TEACHER);
        $teacherAssignmentRecords = User::query()->where('role', User::ROLE_TEACHER)->withCount(['teachingSubjects', 'teachingCourses'])
            ->get(['id', 'name', 'email', 'status', 'last_login_at', 'updated_at']);
        $teachersWithAssignments = $teacherAssignmentRecords->filter(fn (User $teacher) => $teacher->teaching_subjects_count > 0 || $teacher->teaching_courses_count > 0)->count();
        $teacherStats = [
            'total' => (clone $teacherQuery)->count(),
            'active' => (clone $teacherQuery)->where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => (clone $teacherQuery)->where('status', User::STATUS_SUSPENDED)->count(),
            'disabled' => (clone $teacherQuery)->where('status', User::STATUS_DISABLED)->count(),
            'with_profile' => (clone $teacherQuery)->whereHas('teacherProfile')->count(),
            'with_subjects' => (clone $teacherQuery)->whereHas('teachingSubjects')->count(),
            'with_courses' => (clone $teacherQuery)->whereHas('teachingCourses')->count(),
            'with_assignments' => $teachersWithAssignments,
            'unassigned' => max(0, (clone $teacherQuery)->count() - $teachersWithAssignments),
            'subject_assignments' => $teacherAssignmentRecords->sum('teaching_subjects_count'),
            'course_assignments' => $teacherAssignmentRecords->sum('teaching_courses_count'),
            'recent_logins_30_days' => (clone $teacherQuery)->where('last_login_at', '>=', $thirtyDaysAgo)->count(),
        ];
        $teacherStats['profile_completion_rate'] = $teacherStats['total'] > 0 ? round(($teacherStats['with_profile'] / $teacherStats['total']) * 100, 1) : 0.0;
        $teacherStats['assignment_rate'] = $teacherStats['total'] > 0 ? round(($teacherStats['with_assignments'] / $teacherStats['total']) * 100, 1) : 0.0;
        $recentTeachers = User::query()->where('role', User::ROLE_TEACHER)
            ->with(['teacherProfile:id,user_id,employee_code,qualification,specialization,experience_years'])
            ->withCount(['teachingSubjects', 'teachingCourses'])->latest('updated_at')->limit(6)
            ->get(['id', 'name', 'email', 'status', 'last_login_at', 'updated_at']);

        $adminActivityStats = [
            'changes_24_hours' => User::query()->where('updated_at', '>=', $oneDayAgo)->count() + Course::query()->where('updated_at', '>=', $oneDayAgo)->count() + Lesson::query()->where('updated_at', '>=', $oneDayAgo)->count() + Assessment::query()->where('updated_at', '>=', $oneDayAgo)->count(),
            'changes_7_days' => User::query()->where('updated_at', '>=', $sevenDaysAgo)->count() + Course::query()->where('updated_at', '>=', $sevenDaysAgo)->count() + Lesson::query()->where('updated_at', '>=', $sevenDaysAgo)->count() + Assessment::query()->where('updated_at', '>=', $sevenDaysAgo)->count(),
            'users_7_days' => User::query()->where('updated_at', '>=', $sevenDaysAgo)->count(),
            'courses_7_days' => Course::query()->where('updated_at', '>=', $sevenDaysAgo)->count(),
            'lessons_7_days' => Lesson::query()->where('updated_at', '>=', $sevenDaysAgo)->count(),
            'assessments_7_days' => Assessment::query()->where('updated_at', '>=', $sevenDaysAgo)->count(),
        ];

        $recentAdminActivity = collect()
            ->concat(User::query()->latest('updated_at')->limit(5)->get(['id', 'name', 'email', 'role', 'status', 'created_at', 'updated_at'])->map(fn (User $user) => [
                'type' => 'User', 'title' => $user->name, 'description' => ucfirst(str_replace('_', ' ', $user->role)).' · '.ucfirst($user->status), 'action' => $user->created_at?->equalTo($user->updated_at) ? 'Created' : 'Updated', 'occurred_at' => $user->updated_at, 'url' => route('admin.users.edit', $user),
            ]))
            ->concat(Course::query()->latest('updated_at')->limit(5)->get(['id', 'title', 'is_published', 'created_at', 'updated_at'])->map(fn (Course $course) => [
                'type' => 'Course', 'title' => $course->title, 'description' => $course->is_published ? 'Published course' : 'Draft course', 'action' => $course->created_at?->equalTo($course->updated_at) ? 'Created' : 'Updated', 'occurred_at' => $course->updated_at, 'url' => route('admin.courses.edit', $course),
            ]))
            ->concat(Lesson::query()->latest('updated_at')->limit(5)->get(['id', 'title', 'is_published', 'created_at', 'updated_at'])->map(fn (Lesson $lesson) => [
                'type' => 'Lesson', 'title' => $lesson->title, 'description' => $lesson->is_published ? 'Published lesson' : 'Draft lesson', 'action' => $lesson->created_at?->equalTo($lesson->updated_at) ? 'Created' : 'Updated', 'occurred_at' => $lesson->updated_at, 'url' => route('admin.lessons.edit', $lesson),
            ]))
            ->concat(Assessment::query()->with('practiceResource:id,title')->latest('updated_at')->limit(5)->get()->map(fn (Assessment $assessment) => [
                'type' => 'Assessment', 'title' => $assessment->practiceResource?->title ?? 'Assessment #'.$assessment->id, 'description' => $assessment->is_published ? 'Published assessment' : 'Draft assessment', 'action' => $assessment->created_at?->equalTo($assessment->updated_at) ? 'Created' : 'Updated', 'occurred_at' => $assessment->updated_at, 'url' => route('admin.assessments.edit', $assessment),
            ]))
            ->filter(fn (array $activity) => $activity['occurred_at'] !== null)->sortByDesc('occurred_at')->take(12)->values();

        $dashboardFilterOptions = [
            'boards' => $academicProfileService->boards(),
            'standards' => $academicProfileService->standards(),
            'academic_years' => $academicProfileService->academicYears(),
        ];

        $managementAreas = [
            ['label' => 'Users', 'description' => 'Accounts, roles, status and bulk management.', 'route' => 'admin.users.index'],
            ['label' => 'Learners', 'description' => 'Learner profiles, progress and assessment activity.', 'route' => 'admin.learners.index'],
            ['label' => 'Teachers', 'description' => 'Teacher profiles and course or subject assignments.', 'route' => 'admin.teachers.index'],
            ['label' => 'Subjects', 'description' => 'Learning subject categories and publishing.', 'route' => 'admin.subjects.index'],
            ['label' => 'Courses', 'description' => 'Course structure, authoring and publishing.', 'route' => 'admin.courses.index'],
            ['label' => 'Lessons', 'description' => 'Lesson content, ISL learning and resources.', 'route' => 'admin.lessons.index'],
            ['label' => 'Assessments', 'description' => 'Quizzes, questions and learner assessment results.', 'route' => 'admin.assessments.index'],
            ['label' => 'Media', 'description' => 'Images, videos and learning media library.', 'route' => 'admin.media.index'],
        ];

        return view('admin.dashboard', compact(
            'userStats', 'recentUsers', 'contentStats', 'academicStats', 'recentCourses', 'learningStats',
            'recentLearningActivities', 'recentLearningProgress', 'assessmentStats', 'recentAssessmentAttempts',
            'teacherStats', 'recentTeachers', 'adminActivityStats', 'recentAdminActivity', 'dashboardFilterOptions', 'managementAreas'
        ));
    }
}
