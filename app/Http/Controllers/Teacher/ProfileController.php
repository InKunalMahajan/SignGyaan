<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $profile = TeacherProfile::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        return view('teacher.profile', [
            'user' => $request->user(),
            'profile' => $profile,
            'languages' => [
                'english' => 'English',
                'marathi' => 'Marathi',
                'hindi' => 'Hindi',
            ],
            'communicationModes' => [
                'isl' => 'Indian Sign Language (ISL)',
                'text' => 'Text',
                'both' => 'ISL + Text',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:120'],
            'qualification' => ['nullable', 'string', 'max:160'],
            'preferred_language' => ['required', Rule::in(['english', 'marathi', 'hindi'])],
            'communication_mode' => ['required', Rule::in(['isl', 'text', 'both'])],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->teacherProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'designation' => $validated['designation'] ?? null,
                'qualification' => $validated['qualification'] ?? null,
                'preferred_language' => $validated['preferred_language'],
                'communication_mode' => $validated['communication_mode'],
                'bio' => $validated['bio'] ?? null,
            ]
        );

        return back()->with('status', 'Teacher profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'Password changed successfully.');
    }
}
