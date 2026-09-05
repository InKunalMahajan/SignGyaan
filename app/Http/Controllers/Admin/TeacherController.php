<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->with('teacherProfile')
            ->withCount(['teachingSubjects', 'teachingCourses']);

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('teacherProfile', fn ($profile) => $profile->where('employee_code', 'like', "%{$search}%"));
            });
        }

        if (in_array($request->input('status'), [User::STATUS_ACTIVE, User::STATUS_SUSPENDED, User::STATUS_DISABLED], true)) {
            $query->where('status', $request->input('status'));
        }

        return view('admin.teachers.index', [
            'teachers' => $query->orderBy('name')->paginate(20)->withQueryString(),
            'totalTeachers' => User::query()->where('role', User::ROLE_TEACHER)->count(),
            'activeTeachers' => User::query()->where('role', User::ROLE_TEACHER)->where('status', User::STATUS_ACTIVE)->count(),
            'assignedTeachers' => User::query()->where('role', User::ROLE_TEACHER)->where(function ($builder) {
                $builder->has('teachingSubjects')->orHas('teachingCourses');
            })->count(),
        ]);
    }

    public function edit(User $teacher): View
    {
        abort_unless($teacher->isTeacher(), 404);

        $teacher->load(['teacherProfile', 'teachingSubjects', 'teachingCourses']);

        return view('admin.teachers.edit', [
            'teacher' => $teacher,
            'subjects' => Subject::query()->orderBy('sort_order')->orderBy('name')->get(),
            'courses' => Course::query()->with('subject')->orderBy('subject_id')->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->isTeacher(), 404);

        $validated = $request->validate([
            'employee_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('teacher_profiles', 'employee_code')->ignore($teacher->teacherProfile?->id),
            ],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['integer', 'exists:subjects,id'],
            'courses' => ['nullable', 'array'],
            'courses.*' => ['integer', 'exists:courses,id'],
        ]);

        TeacherProfile::query()->updateOrCreate(
            ['user_id' => $teacher->id],
            [
                'employee_code' => filled($validated['employee_code'] ?? null) ? trim((string) $validated['employee_code']) : null,
                'qualification' => filled($validated['qualification'] ?? null) ? trim((string) $validated['qualification']) : null,
                'specialization' => filled($validated['specialization'] ?? null) ? trim((string) $validated['specialization']) : null,
                'experience_years' => $validated['experience_years'] ?? null,
                'bio' => filled($validated['bio'] ?? null) ? trim((string) $validated['bio']) : null,
            ]
        );

        $teacher->teachingSubjects()->sync($validated['subjects'] ?? []);
        $teacher->teachingCourses()->sync($validated['courses'] ?? []);

        return redirect()
            ->route('admin.teachers.edit', $teacher)
            ->with('status', 'Teacher profile and assignments updated successfully.');
    }
}
