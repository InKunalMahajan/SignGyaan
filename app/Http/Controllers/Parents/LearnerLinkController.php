<?php

namespace App\Http\Controllers\Parents;

use App\Http\Controllers\Controller;
use App\Models\ParentLearnerLink;
use App\Models\User;
use App\Support\LearnerProgressSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LearnerLinkController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'learner_email' => ['required', 'email', 'max:255'],
            'relationship' => ['required', Rule::in(ParentLearnerLink::RELATIONSHIPS)],
        ]);

        $learner = User::query()
            ->where('email', $validated['learner_email'])
            ->where('role', 'learner')
            ->where('is_active', true)
            ->first();

        if (! $learner) {
            throw ValidationException::withMessages([
                'learner_email' => 'No active Learner account was found for that email address.',
            ]);
        }

        $link = ParentLearnerLink::query()
            ->where('parent_user_id', $request->user()->id)
            ->where('learner_user_id', $learner->id)
            ->first();

        if ($link?->status === 'approved') {
            throw ValidationException::withMessages([
                'learner_email' => 'This Learner is already linked to your account.',
            ]);
        }

        if ($link?->status === 'pending') {
            throw ValidationException::withMessages([
                'learner_email' => 'A linking request is already waiting for this Learner.',
            ]);
        }

        ParentLearnerLink::updateOrCreate(
            [
                'parent_user_id' => $request->user()->id,
                'learner_user_id' => $learner->id,
            ],
            [
                'relationship' => $validated['relationship'],
                'status' => 'pending',
                'responded_at' => null,
                'approved_at' => null,
            ]
        );

        return back()->with('status', 'Link request sent. The Learner must approve it before you can view their learning summary.');
    }

    public function show(
        Request $request,
        ParentLearnerLink $link,
        LearnerProgressSummary $progressSummary
    ): View {
        abort_unless($link->parent_user_id === $request->user()->id, 403);
        abort_unless($link->isApproved(), 403);

        $link->load(['learner.learnerProfile']);
        $progress = $progressSummary->build($link->learner);

        return view('parents.learner', [
            'link' => $link,
            'learner' => $link->learner,
            'profile' => $link->learner->learnerProfile,
            'progress' => $progress,
        ]);
    }

    public function destroy(Request $request, ParentLearnerLink $link): RedirectResponse
    {
        abort_unless($link->parent_user_id === $request->user()->id, 403);

        $link->delete();

        return redirect()->route('parents.profile.show')->with('status', 'Learner link removed.');
    }
}
