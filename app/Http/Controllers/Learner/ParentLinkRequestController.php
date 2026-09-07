<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\ParentLearnerLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParentLinkRequestController extends Controller
{
    public function index(Request $request): View
    {
        $links = ParentLearnerLink::query()
            ->where('learner_user_id', $request->user()->id)
            ->with('parent.parentProfile')
            ->latest()
            ->get();

        return view('learner.parent-links', [
            'links' => $links,
        ]);
    }

    public function respond(Request $request, ParentLearnerLink $link): RedirectResponse
    {
        abort_unless($link->learner_user_id === $request->user()->id, 403);
        abort_unless($link->status === 'pending', 409);

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approve', 'decline'])],
        ]);

        $approved = $validated['decision'] === 'approve';

        $link->update([
            'status' => $approved ? 'approved' : 'declined',
            'responded_at' => now(),
            'approved_at' => $approved ? now() : null,
        ]);

        return back()->with('status', $approved ? 'Parent access approved.' : 'Parent link request declined.');
    }

    public function destroy(Request $request, ParentLearnerLink $link): RedirectResponse
    {
        abort_unless($link->learner_user_id === $request->user()->id, 403);

        $link->delete();

        return back()->with('status', 'Parent access removed.');
    }
}
