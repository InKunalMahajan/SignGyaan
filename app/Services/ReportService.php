<?php

namespace App\Services;

use App\Models\AssessmentAttempt;
use App\Models\CourseMastery;
use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Support\Collection;

class ReportService
{
    public function forUser(User $user): array
    {
        return match ($user->role) {
            'learner' => $this->learner($user),
            'teacher' => $this->teacher($user),
            'parents' => $this->parent($user),
            'admin' => $this->admin(),
            default => ['summary' => [], 'rows' => collect()],
        };
    }

    private function learner(User $user): array
    {
        $masteries = CourseMastery::with('course')->where('learner_id', $user->id)->get();
        $attempts = AssessmentAttempt::with('assessment.course')->where('learner_id', $user->id)->where('status', 'completed')->get();

        return [
            'title' => 'My Progress Report',
            'summary' => [
                'Courses tracked' => $masteries->count(),
                'Average mastery' => $this->average($masteries->pluck('mastery_score')),
                'Assessments completed' => $attempts->count(),
                'Average assessment' => $this->average($attempts->pluck('percentage')),
            ],
            'rows' => $masteries->map(fn ($m) => [
                'Learner' => $user->name,
                'Course' => $m->course?->title ?? 'Course',
                'Lessons' => $m->lessons_completed.' / '.$m->lessons_total,
                'Assessment %' => $this->number($m->assessment_percentage),
                'Mastery %' => $this->number($m->mastery_score),
                'Level' => $m->levelLabel(),
            ]),
        ];
    }

    private function teacher(User $user): array
    {
        $classes = LearningClass::with(['learners', 'courses'])->where('teacher_id', $user->id)->get();
        $learnerIds = $classes->flatMap->learners->pluck('id')->unique()->values();
        $courseIds = $classes->flatMap->courses->pluck('id')->unique()->values();
        $masteries = CourseMastery::with(['learner', 'course'])
            ->whereIn('learner_id', $learnerIds)
            ->whereIn('course_id', $courseIds)
            ->get();
        $attempts = AssessmentAttempt::whereHas('assessment', fn ($q) => $q->where('created_by', $user->id))
            ->where('status', 'completed')->get();

        return [
            'title' => 'Teaching Report',
            'summary' => [
                'Classes' => $classes->count(),
                'Learners' => $learnerIds->count(),
                'Courses' => $courseIds->count(),
                'Average mastery' => $this->average($masteries->pluck('mastery_score')),
                'Completed attempts' => $attempts->count(),
            ],
            'rows' => $masteries->map(fn ($m) => [
                'Learner' => $m->learner?->name ?? 'Learner',
                'Course' => $m->course?->title ?? 'Course',
                'Lessons' => $m->lessons_completed.' / '.$m->lessons_total,
                'Assessment %' => $this->number($m->assessment_percentage),
                'Mastery %' => $this->number($m->mastery_score),
                'Level' => $m->levelLabel(),
            ]),
        ];
    }

    private function parent(User $user): array
    {
        $learners = User::whereHas('parentLinks', fn ($q) => $q->where('parent_user_id', $user->id)->where('status', 'approved'))->get();
        $ids = $learners->pluck('id');
        $masteries = CourseMastery::with(['learner', 'course'])->whereIn('learner_id', $ids)->get();

        return [
            'title' => 'Learner Progress Report',
            'summary' => [
                'Approved learners' => $learners->count(),
                'Courses tracked' => $masteries->count(),
                'Average mastery' => $this->average($masteries->pluck('mastery_score')),
            ],
            'rows' => $masteries->map(fn ($m) => [
                'Learner' => $m->learner?->name ?? 'Learner',
                'Course' => $m->course?->title ?? 'Course',
                'Lessons' => $m->lessons_completed.' / '.$m->lessons_total,
                'Assessment %' => $this->number($m->assessment_percentage),
                'Mastery %' => $this->number($m->mastery_score),
                'Level' => $m->levelLabel(),
            ]),
        ];
    }

    private function admin(): array
    {
        $masteries = CourseMastery::with(['learner', 'course'])->get();

        return [
            'title' => 'Platform Report',
            'summary' => [
                'Users' => User::count(),
                'Learners' => User::where('role', 'learner')->count(),
                'Teachers' => User::where('role', 'teacher')->count(),
                'Classes' => LearningClass::count(),
                'Average mastery' => $this->average($masteries->pluck('mastery_score')),
            ],
            'rows' => $masteries->map(fn ($m) => [
                'Learner' => $m->learner?->name ?? 'Learner',
                'Course' => $m->course?->title ?? 'Course',
                'Lessons' => $m->lessons_completed.' / '.$m->lessons_total,
                'Assessment %' => $this->number($m->assessment_percentage),
                'Mastery %' => $this->number($m->mastery_score),
                'Level' => $m->levelLabel(),
            ]),
        ];
    }

    private function average(Collection $values): string
    {
        $values = $values->filter(fn ($value) => $value !== null);
        return $values->isEmpty() ? '0.00%' : number_format((float) $values->avg(), 2).'%';
    }

    private function number($value): string
    {
        return number_format((float) ($value ?? 0), 2).'%';
    }
}
