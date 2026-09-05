<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\LearningProgress;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $userStats = [
            'total' => User::query()->count(),
            'learners' => User::query()->where('role', User::ROLE_LEARNER)->count(),
            'teachers' => User::query()->where('role', User::ROLE_TEACHER)->count(),
            'administrators' => User::query()
                ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN])
                ->count(),
            'active' => User::query()->where('status', User::STATUS_ACTIVE)->count(),
        ];

        $contentStats = [
            'subjects' => Subject::query()->count(),
            'courses' => Course::query()->count(),
            'lessons' => Lesson::query()->count(),
            'assessments' => Assessment::query()->count(),
        ];

        $learningStats = [
            'tracked_courses' => LearningProgress::query()->count(),
            'completed_courses' => LearningProgress::query()->whereNotNull('completed_at')->count(),
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
            'userStats',
            'contentStats',
            'learningStats',
            'managementAreas'
        ));
    }
}
