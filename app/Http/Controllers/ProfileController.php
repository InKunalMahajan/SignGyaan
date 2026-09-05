<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $learningProgress = $user->learningProgress()
            ->latest('last_accessed_at')
            ->get();

        return view('profile', [
            'user' => $user,
            'learningProgress' => $learningProgress,
            'coursesStarted' => $learningProgress->count(),
            'completedCourses' => $learningProgress->whereNotNull('completed_at')->count(),
            'completedLessons' => $learningProgress->sum(fn ($progress) => $progress->completedLessonsCount()),
        ]);
    }

    public function accessibility(Request $request): View
    {
        return view('accessibility-preferences', [
            'user' => $request->user(),
        ]);
    }

    public function notificationPreferences(Request $request): View
    {
        return view('notification-preferences', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $newEmail = strtolower(trim($validated['email']));

        if ($newEmail !== strtolower($user->email)) {
            $user->email_verified_at = null;
        }

        $user->name = trim($validated['name']);
        $user->email = $newEmail;
        $user->save();

        return back()->with('profile_status', 'Your profile details were updated successfully.');
    }

    public function updateAccessibility(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('accessibility', [
            'captions' => ['nullable', Rule::in(['prefer', 'manual'])],
            'transcript' => ['nullable', Rule::in(['show', 'hide'])],
            'simple_summary' => ['nullable', Rule::in(['show', 'hide'])],
            'reduced_motion' => ['nullable', Rule::in(['on', 'system'])],
        ]);

        $request->user()->update([
            'accessibility_preferences' => [
                'captions' => $validated['captions'] ?? 'manual',
                'transcript' => $validated['transcript'] ?? 'show',
                'simple_summary' => $validated['simple_summary'] ?? 'show',
                'reduced_motion' => $validated['reduced_motion'] ?? 'system',
            ],
        ]);

        return back()->with('accessibility_status', 'Accessibility preferences were saved.');
    }

    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('notifications', [
            'enabled' => ['nullable', 'boolean'],
            'learning' => ['nullable', 'boolean'],
            'assessment' => ['nullable', 'boolean'],
            'milestone' => ['nullable', 'boolean'],
            'general' => ['nullable', 'boolean'],
        ]);

        $request->user()->update([
            'notification_preferences' => [
                'enabled' => (bool) ($validated['enabled'] ?? false),
                'learning' => (bool) ($validated['learning'] ?? false),
                'assessment' => (bool) ($validated['assessment'] ?? false),
                'milestone' => (bool) ($validated['milestone'] ?? false),
                'general' => (bool) ($validated['general'] ?? false),
            ],
        ]);

        return back()->with('notification_status', 'Notification preferences were saved.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('password_status', 'Your password was changed successfully.');
    }
}
