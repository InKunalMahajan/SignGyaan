<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request, GlobalSearchService $search): View
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        $query = trim((string) $request->query('q', ''));
        $user = $request->user();
        $results = mb_strlen($query) >= 2 ? $search->search($user, $query) : [];

        $layout = match ($user->role) {
            'learner' => 'learner.layout',
            'teacher' => 'teacher.layout',
            'parents' => 'parents.layout',
            'admin' => 'admin.layout',
            default => 'learner.layout',
        };

        return view('search.index', compact('query', 'results', 'layout'));
    }
}
