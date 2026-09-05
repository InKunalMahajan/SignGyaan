<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Models\VocabularyTerm;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicVocabularyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $subjectSlug = trim((string) $request->query('subject', ''));
        $courseSlug = trim((string) $request->query('course', ''));

        $subjects = Subject::query()
            ->published()
            ->whereHas('vocabularyTerms', fn ($query) => $query->published())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $courses = Course::query()
            ->published()
            ->whereHas('subject', fn ($query) => $query->published())
            ->whereHas('vocabularyTerms', fn ($query) => $query->published())
            ->when($subjectSlug !== '', function ($query) use ($subjectSlug) {
                $query->whereHas('subject', fn ($subjectQuery) => $subjectQuery->where('slug', $subjectSlug));
            })
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $terms = VocabularyTerm::query()
            ->published()
            ->where(function ($query) {
                $query
                    ->whereNull('subject_id')
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->published());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('course_id')
                    ->orWhereHas('course', fn ($courseQuery) => $courseQuery
                        ->published()
                        ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()));
            })
            ->with([
                'subject',
                'course.subject',
                'mediaAsset' => fn ($query) => $query->published()->where('media_type', 'video'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('term', 'like', "%{$search}%")
                        ->orWhere('meaning', 'like', "%{$search}%")
                        ->orWhere('example', 'like', "%{$search}%");
                });
            })
            ->when($subjectSlug !== '', function ($query) use ($subjectSlug) {
                $query->whereHas('subject', fn ($subjectQuery) => $subjectQuery
                    ->published()
                    ->where('slug', $subjectSlug));
            })
            ->when($courseSlug !== '', function ($query) use ($courseSlug) {
                $query->whereHas('course', fn ($courseQuery) => $courseQuery
                    ->published()
                    ->where('slug', $courseSlug)
                    ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()));
            })
            ->orderBy('sort_order')
            ->orderBy('term')
            ->paginate(24)
            ->withQueryString();

        return view('pages.vocabulary.index', [
            'terms' => $terms,
            'subjects' => $subjects,
            'courses' => $courses,
            'search' => $search,
            'subjectSlug' => $subjectSlug,
            'courseSlug' => $courseSlug,
        ]);
    }

    public function show(string $vocabularyTerm): View
    {
        $term = VocabularyTerm::query()
            ->published()
            ->where('slug', $vocabularyTerm)
            ->where(function ($query) {
                $query
                    ->whereNull('subject_id')
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->published());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('course_id')
                    ->orWhereHas('course', fn ($courseQuery) => $courseQuery
                        ->published()
                        ->whereHas('subject', fn ($subjectQuery) => $subjectQuery->published()));
            })
            ->with([
                'subject',
                'course.subject',
                'mediaAsset' => fn ($query) => $query->published()->where('media_type', 'video'),
            ])
            ->firstOrFail();

        return view('pages.vocabulary.show', compact('term'));
    }
}
