<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\ParentLearnerLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    public function search(User $user, string $query): array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return [];
        }

        return match ($user->role) {
            'learner' => $this->learner($user, $query),
            'teacher' => $this->teacher($user, $query),
            'parents' => $this->parents($user, $query),
            'admin' => $this->admin($query),
            default => [],
        };
    }

    private function learner(User $user, string $query): array
    {
        $classes = LearningClass::query()
            ->whereHas('learners', fn (Builder $builder) => $builder->where('users.id', $user->id))
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['name', 'code', 'subject', 'level', 'academic_year']))
            ->limit(8)->get()
            ->map(fn (LearningClass $class) => $this->result('Class', $class->name, $class->code ?: $class->academic_year, route('learner.classes.show', $class)));

        $courseScope = fn (Builder $builder) => $builder->whereHas('classes.learners', fn (Builder $learners) => $learners->where('users.id', $user->id));

        $courses = Course::query()->where($courseScope)
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'level', 'description']))
            ->limit(8)->get()
            ->map(function (Course $course) use ($user) {
                $class = $course->classes()->whereHas('learners', fn (Builder $builder) => $builder->where('users.id', $user->id))->first();
                return $this->result('Course', $course->title, $course->level, $class ? route('learner.classes.courses.show', [$class, $course]) : route('learner.classes.index'));
            });

        $chapters = CourseUnit::query()->with('course')->whereHas('course', $courseScope)
            ->where('is_active', true)
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'description']))
            ->limit(8)->get()
            ->map(function (CourseUnit $unit) use ($user) {
                $class = $unit->course->classes()->whereHas('learners', fn (Builder $builder) => $builder->where('users.id', $user->id))->first();
                $url = $class ? route('learner.classes.courses.show', [$class, $unit->course]).'#chapter-'.$unit->id : route('learner.classes.index');
                return $this->result('Chapter', $unit->title, $unit->course->title, $url);
            });

        $lessons = Lesson::query()->with('unit.course')->where('status', 'published')
            ->whereHas('unit.course', $courseScope)
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'summary', 'notes']))
            ->limit(8)->get()
            ->map(function (Lesson $lesson) use ($user) {
                $course = $lesson->unit->course;
                $class = $course->classes()->whereHas('learners', fn (Builder $builder) => $builder->where('users.id', $user->id))->first();
                $url = $class ? route('learner.classes.courses.lessons.show', [$class, $course, $lesson]) : route('learner.classes.index');
                return $this->result('Lesson', $lesson->title, $course->title.' · '.$lesson->unit->title, $url);
            });

        return $this->groups([
            'Classes' => $classes,
            'Courses' => $courses,
            'Chapters' => $chapters,
            'Lessons' => $lessons,
        ]);
    }

    private function teacher(User $user, string $query): array
    {
        $classes = LearningClass::query()->where('teacher_id', $user->id)
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['name', 'code', 'subject', 'level', 'academic_year']))
            ->limit(8)->get()
            ->map(fn (LearningClass $class) => $this->result('Class', $class->name, $class->code ?: $class->academic_year, route('teacher.classes.show', $class)));

        $courseScope = fn (Builder $builder) => $builder
            ->where('created_by', $user->id)
            ->orWhereHas('classes', fn (Builder $classes) => $classes->where('teacher_id', $user->id));

        $courses = Course::query()->where(fn (Builder $builder) => $courseScope($builder))
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'level', 'description']))
            ->limit(8)->get()
            ->map(fn (Course $course) => $this->result('Course', $course->title, $course->level, route('teacher.courses.curriculum.show', $course)));

        $chapters = CourseUnit::query()->with('course')->whereHas('course', fn (Builder $builder) => $courseScope($builder))
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'description']))
            ->limit(8)->get()
            ->map(fn (CourseUnit $unit) => $this->result('Chapter', $unit->title, $unit->course->title, route('teacher.courses.curriculum.show', $unit->course).'#chapter-'.$unit->id));

        $lessons = Lesson::query()->with('unit.course')->whereHas('unit.course', fn (Builder $builder) => $courseScope($builder))
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'summary', 'notes']))
            ->limit(8)->get()
            ->map(fn (Lesson $lesson) => $this->result('Lesson', $lesson->title, $lesson->unit->course->title.' · '.$lesson->unit->title, route('teacher.courses.curriculum.show', $lesson->unit->course).'#lesson-'.$lesson->id));

        $assessments = Assessment::query()->where('created_by', $user->id)
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'instructions', 'status']))
            ->limit(8)->get()
            ->map(fn (Assessment $assessment) => $this->result('Assessment', $assessment->title, ucfirst($assessment->status), route('teacher.assessments.edit', $assessment)));

        return $this->groups([
            'Classes' => $classes,
            'Courses' => $courses,
            'Chapters' => $chapters,
            'Lessons' => $lessons,
            'Assessments' => $assessments,
        ]);
    }

    private function parents(User $user, string $query): array
    {
        $links = ParentLearnerLink::query()->with('learner')
            ->where('parent_user_id', $user->id)
            ->where('status', 'approved')
            ->whereHas('learner', fn (Builder $builder) => $this->match($builder, $query, ['name', 'email']))
            ->limit(10)->get()
            ->map(fn (ParentLearnerLink $link) => $this->result('Learner', $link->learner->name, ucfirst($link->relationship), route('parents.learners.show', $link)));

        return $this->groups(['Linked learners' => $links]);
    }

    private function admin(string $query): array
    {
        $users = User::query()
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['name', 'email', 'role']))
            ->limit(10)->get()
            ->map(fn (User $user) => $this->result('User', $user->name, ucfirst($user->role).' · '.$user->email, route('admin.users.show', $user)));

        $classes = LearningClass::query()
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['name', 'code', 'subject', 'level', 'academic_year']))
            ->limit(8)->get()
            ->map(fn (LearningClass $class) => $this->result('Class', $class->name, $class->code ?: $class->academic_year, route('admin.teaching.index')));

        $courses = Course::query()
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'level', 'description']))
            ->limit(8)->get()
            ->map(fn (Course $course) => $this->result('Course', $course->title, $course->level, route('admin.curriculum.content.index')));

        $chapters = CourseUnit::query()->with('course')
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'description']))
            ->limit(8)->get()
            ->map(fn (CourseUnit $unit) => $this->result('Chapter', $unit->title, $unit->course?->title, route('admin.curriculum.content.index')));

        $lessons = Lesson::query()->with('unit.course')
            ->where(fn (Builder $builder) => $this->match($builder, $query, ['title', 'summary', 'notes', 'status']))
            ->limit(8)->get()
            ->map(fn (Lesson $lesson) => $this->result('Lesson', $lesson->title, $lesson->unit?->course?->title, route('admin.curriculum.content.index')));

        return $this->groups([
            'Users' => $users,
            'Classes' => $classes,
            'Courses' => $courses,
            'Chapters' => $chapters,
            'Lessons' => $lessons,
        ]);
    }

    private function match(Builder $builder, string $query, array $columns): void
    {
        $term = '%'.addcslashes($query, '%_\\').'%';

        $builder->where(function (Builder $nested) use ($columns, $term): void {
            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $nested->{$method}($column, 'like', $term);
            }
        });
    }

    private function groups(array $groups): array
    {
        return collect($groups)
            ->filter(fn (Collection $items) => $items->isNotEmpty())
            ->map(fn (Collection $items) => $items->values()->all())
            ->all();
    }

    private function result(string $type, string $title, ?string $context, string $url): array
    {
        return compact('type', 'title', 'context', 'url');
    }
}
