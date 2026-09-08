<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\LearnerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private const PROFILE_DEFAULTS = [
        'preferred_language' => 'english',
        'communication_mode' => 'both',
        'captions_enabled' => true,
        'high_contrast' => false,
        'reduced_motion' => false,
        'text_size' => 'standard',
    ];

    public function show(Request $request): View
    {
        $profile = LearnerProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            self::PROFILE_DEFAULTS
        );

        return view('learner.profile', [
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
            'textSizes' => [
                'standard' => 'Standard',
                'large' => 'Large',
                'xlarge' => 'Extra large',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'education_level' => ['nullable', 'string', 'max:100'],
            'class_grade' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:160'],
            'preferred_language' => ['required', Rule::in(['english', 'marathi', 'hindi'])],
            'communication_mode' => ['required', Rule::in(['isl', 'text', 'both'])],
            'captions_enabled' => ['required', 'boolean'],
            'high_contrast' => ['required', 'boolean'],
            'reduced_motion' => ['required', 'boolean'],
            'text_size' => ['required', Rule::in(['standard', 'large', 'xlarge'])],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $user->learnerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'education_level' => $validated['education_level'] ?? null,
                    'class_grade' => $validated['class_grade'] ?? null,
                    'institution' => $validated['institution'] ?? null,
                    'preferred_language' => $validated['preferred_language'],
                    'communication_mode' => $validated['communication_mode'],
                    'captions_enabled' => $validated['captions_enabled'],
                    'high_contrast' => $validated['high_contrast'],
                    'reduced_motion' => $validated['reduced_motion'],
                    'text_size' => $validated['text_size'],
                ]
            );
        });

        return back()->with('status', 'Profile and accessibility preferences updated.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $profile = LearnerProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            self::PROFILE_DEFAULTS
        );

        $oldPath = $profile->avatar_path;
        $path = $validated['avatar']->store('learner-avatars', 'public');

        $profile->update(['avatar_path' => $path]);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return back()->with('status', 'Profile photo updated.');
    }

    public function removeAvatar(Request $request): RedirectResponse
    {
        $profile = LearnerProfile::where('user_id', $request->user()->id)->first();

        if ($profile?->avatar_path) {
            Storage::disk('public')->delete($profile->avatar_path);
            $profile->update(['avatar_path' => null]);
        }

        return back()->with('status', 'Profile photo removed.');
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
