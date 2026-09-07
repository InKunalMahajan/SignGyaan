<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use App\Models\ParentLearnerLink;
use App\Models\ParentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $profile = ParentProfile::firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $links = ParentLearnerLink::query()
            ->where('parent_user_id', $request->user()->id)
            ->with(['learner.learnerProfile'])
            ->latest()
            ->get();

        return view('parents.profile', [
            'user' => $request->user(),
            'profile' => $profile,
            'links' => $links,
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
            'relationships' => [
                'parent' => 'Parent',
                'guardian' => 'Guardian',
                'family' => 'Family member',
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
            'preferred_language' => ['required', Rule::in(['english', 'marathi', 'hindi'])],
            'communication_mode' => ['required', Rule::in(['isl', 'text', 'both'])],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            $user->parentProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $validated['phone'] ?? null,
                    'preferred_language' => $validated['preferred_language'],
                    'communication_mode' => $validated['communication_mode'],
                ]
            );
        });

        return back()->with('status', 'Parent profile updated.');
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
