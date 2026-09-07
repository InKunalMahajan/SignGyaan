<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const ROLES = ['learner', 'parents', 'teacher', 'admin'];

    public function index(Request $request): RedirectResponse|View
    {
        if ($request->user()) {
            return redirect()->route('dashboard.role', ['role' => $request->user()->role]);
        }

        return $this->render('guest');
    }

    public function guest(): View
    {
        return $this->render('guest');
    }

    public function show(Request $request, string $role): View
    {
        abort_unless(in_array($role, self::ROLES, true), 404);
        abort_unless($request->user()?->hasRole($role), 403);

        return $this->render($role);
    }

    private function render(string $role): View
    {
        $dashboards = [
            'learner' => [
                'label' => 'Learner',
                'eyebrow' => 'My Learning',
                'title' => 'Keep learning, one lesson at a time.',
                'description' => 'Continue courses, practise with quizzes, watch ISL lessons, and track your progress.',
                'primary_action' => 'Continue learning',
                'stats' => [
                    ['label' => 'Courses in progress', 'value' => '3', 'helper' => '2 active this week'],
                    ['label' => 'Lessons completed', 'value' => '18', 'helper' => '6 this month'],
                    ['label' => 'Average quiz score', 'value' => '82%', 'helper' => 'Up 7%'],
                ],
                'modules' => [
                    ['title' => 'Continue Learning', 'description' => 'Resume your latest lesson and ISL video.', 'tag' => 'Recommended'],
                    ['title' => 'My Courses', 'description' => 'View enrolled subjects, courses, units, and lessons.', 'tag' => 'Learning'],
                    ['title' => 'Practice & Quizzes', 'description' => 'Test your understanding with accessible practice.', 'tag' => 'Assessment'],
                    ['title' => 'My Progress', 'description' => 'Track completion, scores, streaks, and learning goals.', 'tag' => 'Progress'],
                    ['title' => 'Certificates', 'description' => 'View achievements and completed course certificates.', 'tag' => 'Achievement'],
                    ['title' => 'Saved Lessons', 'description' => 'Open bookmarked videos, notes, and examples.', 'tag' => 'Library'],
                ],
                'updates' => [
                    ['title' => 'Digital Basics · Unit 2', 'meta' => 'Next: Storage Devices · 12 min'],
                    ['title' => 'English Communication · Practice', 'meta' => 'Quiz score: 8/10'],
                    ['title' => 'Computer Skills · Unit 1', 'meta' => '75% complete'],
                ],
            ],
            'parents' => [
                'label' => 'Parents',
                'eyebrow' => 'Family Learning',
                'title' => 'See progress and support learning at home.',
                'description' => 'Follow your learner’s progress, assessment results, goals, and teacher updates in one place.',
                'primary_action' => 'View learner progress',
                'stats' => [
                    ['label' => 'Weekly learning', 'value' => '4h 20m', 'helper' => 'Goal: 5 hours'],
                    ['label' => 'Lessons completed', 'value' => '12', 'helper' => 'This month'],
                    ['label' => 'Assessment average', 'value' => '79%', 'helper' => '3 recent quizzes'],
                ],
                'modules' => [
                    ['title' => 'Learner Progress', 'description' => 'See course, unit, lesson, and assessment progress.', 'tag' => 'Overview'],
                    ['title' => 'Learning Time', 'description' => 'Review weekly learning activity and consistency.', 'tag' => 'Activity'],
                    ['title' => 'Assessment Results', 'description' => 'Understand recent quiz scores and improvement areas.', 'tag' => 'Results'],
                    ['title' => 'Goals', 'description' => 'Follow current goals and celebrate completed milestones.', 'tag' => 'Goals'],
                    ['title' => 'Teacher Updates', 'description' => 'Read important learning notes and announcements.', 'tag' => 'Updates'],
                    ['title' => 'Support Resources', 'description' => 'Find simple ways to support learning at home.', 'tag' => 'Help'],
                ],
                'updates' => [
                    ['title' => 'Weekly goal is nearly complete', 'meta' => '4h 20m of 5h completed'],
                    ['title' => 'New assessment result', 'meta' => 'Digital Basics · 8/10'],
                    ['title' => 'Teacher update', 'meta' => 'Practice Unit 2 before Friday'],
                ],
            ],
            'teacher' => [
                'label' => 'Teacher',
                'eyebrow' => 'Teaching Workspace',
                'title' => 'Plan lessons, assess learning, and support every student.',
                'description' => 'Manage classes, lessons, ISL resources, assessments, learner progress, and announcements.',
                'primary_action' => 'Open my classes',
                'stats' => [
                    ['label' => 'Active classes', 'value' => '5', 'helper' => '128 learners'],
                    ['label' => 'Pending reviews', 'value' => '14', 'helper' => '6 due today'],
                    ['label' => 'Class average', 'value' => '76%', 'helper' => 'Across assessments'],
                ],
                'modules' => [
                    ['title' => 'My Classes', 'description' => 'Open class rosters and learning progress.', 'tag' => 'Classes'],
                    ['title' => 'Lessons & ISL Videos', 'description' => 'Create and organise accessible lesson content.', 'tag' => 'Content'],
                    ['title' => 'Assessments', 'description' => 'Create quizzes, review attempts, and publish feedback.', 'tag' => 'Assessment'],
                    ['title' => 'Learner Progress', 'description' => 'Identify strengths, gaps, and learners needing support.', 'tag' => 'Insights'],
                    ['title' => 'Announcements', 'description' => 'Share class reminders and learning updates.', 'tag' => 'Communication'],
                    ['title' => 'Resources', 'description' => 'Manage notes, examples, worksheets, and links.', 'tag' => 'Library'],
                ],
                'updates' => [
                    ['title' => '6 quiz attempts need review', 'meta' => 'Digital Basics · Class A'],
                    ['title' => 'Lesson draft saved', 'meta' => 'Internet Safety · Unit 3'],
                    ['title' => 'Class B progress', 'meta' => '81% average completion'],
                ],
            ],
            'admin' => [
                'label' => 'Admin',
                'eyebrow' => 'Platform Management',
                'title' => 'Manage SignGyaan from one clear control centre.',
                'description' => 'Oversee users, roles, learning content, courses, reports, publishing, and platform settings.',
                'primary_action' => 'Manage platform',
                'stats' => [
                    ['label' => 'Total users', 'value' => '1,284', 'helper' => '42 new this month'],
                    ['label' => 'Published lessons', 'value' => '216', 'helper' => '18 drafts'],
                    ['label' => 'Active courses', 'value' => '24', 'helper' => '8 subjects'],
                ],
                'modules' => [
                    ['title' => 'Users & Roles', 'description' => 'Manage learner, parent, teacher, and admin access.', 'tag' => 'Access'],
                    ['title' => 'Subjects & Courses', 'description' => 'Manage the Subjects → Courses → Units → Lessons structure.', 'tag' => 'Curriculum'],
                    ['title' => 'Content Review', 'description' => 'Review lessons, ISL videos, notes, and assessments.', 'tag' => 'Publishing'],
                    ['title' => 'Reports & Analytics', 'description' => 'Track engagement, completion, and assessment trends.', 'tag' => 'Reports'],
                    ['title' => 'Announcements', 'description' => 'Publish platform-wide notices and learning updates.', 'tag' => 'Communication'],
                    ['title' => 'Settings', 'description' => 'Configure accessibility, platform, and account settings.', 'tag' => 'System'],
                ],
                'updates' => [
                    ['title' => '18 lessons awaiting review', 'meta' => 'Content workflow'],
                    ['title' => '42 new users this month', 'meta' => 'User growth'],
                    ['title' => 'Platform accessibility check', 'meta' => 'No critical issues in preview'],
                ],
            ],
            'guest' => [
                'label' => 'Guest',
                'eyebrow' => 'Explore SignGyaan',
                'title' => 'Accessible learning starts here.',
                'description' => 'Explore subjects, preview ISL-supported lessons, and discover how SignGyaan makes learning easier to access.',
                'primary_action' => 'Explore subjects',
                'stats' => [
                    ['label' => 'Subjects', 'value' => '8', 'helper' => 'Structured learning paths'],
                    ['label' => 'Sample lessons', 'value' => '24', 'helper' => 'Free to explore'],
                    ['label' => 'Learning format', 'value' => 'ISL + Text', 'helper' => 'Accessible by design'],
                ],
                'modules' => [
                    ['title' => 'Explore Subjects', 'description' => 'Browse structured subjects and courses.', 'tag' => 'Discover'],
                    ['title' => 'Preview ISL Lessons', 'description' => 'See how video, text, examples, and practice work together.', 'tag' => 'ISL'],
                    ['title' => 'How SignGyaan Works', 'description' => 'Learn about Subjects → Courses → Units → Lessons.', 'tag' => 'Guide'],
                    ['title' => 'Accessibility', 'description' => 'Discover accessibility-first learning features.', 'tag' => 'Access'],
                    ['title' => 'For Families', 'description' => 'See how parents can follow and support progress.', 'tag' => 'Parents'],
                    ['title' => 'Join SignGyaan', 'description' => 'Create an account to start your role-based learning experience.', 'tag' => 'Get Started'],
                ],
                'updates' => [
                    ['title' => 'Start with Digital Basics', 'meta' => 'Beginner-friendly sample course'],
                    ['title' => 'Watch an ISL lesson preview', 'meta' => 'Video + notes + practice'],
                    ['title' => 'Explore accessible learning', 'meta' => 'Designed for clear visual navigation'],
                ],
            ],
        ];

        return view('dashboard', [
            'role' => $role,
            'dashboard' => $dashboards[$role],
        ]);
    }
}
