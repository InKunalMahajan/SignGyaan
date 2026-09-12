<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\KnowledgeSource;
use App\Services\RagIndexService;
use App\Services\TeacherRagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class KnowledgeAssistantController extends Controller
{
    public function index(Request $request): View
    {
        return view('teacher.rag.index', $this->pageData($request, null));
    }

    public function storeSource(Request $request, RagIndexService $indexer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'min:20', 'max:50000'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);

        $teacher = $request->user();
        $courseId = isset($validated['course_id']) ? (int) $validated['course_id'] : null;

        if ($courseId !== null && ! $this->coursesFor($teacher->id)->contains('id', $courseId)) {
            abort(403);
        }

        try {
            $indexer->createManualSource(
                $teacher,
                $validated['title'],
                $validated['content'],
                $courseId,
            );
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['rag' => $exception->getMessage()]);
        }

        return back()->with('status', 'Knowledge source added and indexed.');
    }

    public function reindex(Request $request, KnowledgeSource $source, RagIndexService $indexer): RedirectResponse
    {
        $this->authorizeSource($request, $source);

        try {
            $indexer->index($source);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['rag' => $exception->getMessage()]);
        }

        return back()->with('status', 'Knowledge source re-indexed.');
    }

    public function destroy(Request $request, KnowledgeSource $source): RedirectResponse
    {
        $this->authorizeSource($request, $source);
        $source->delete();

        return back()->with('status', 'Knowledge source removed.');
    }

    public function indexCourse(Request $request, Course $course, RagIndexService $indexer): RedirectResponse
    {
        if ((int) $course->created_by !== (int) $request->user()->id) {
            abort(403);
        }

        $course->load(['units' => function ($query): void {
            $query->where('is_active', true)
                ->with(['lessons' => fn ($lessons) => $lessons->where('status', 'published')]);
        }]);

        $indexed = 0;
        $skipped = 0;

        foreach ($course->units as $unit) {
            foreach ($unit->lessons as $lesson) {
                try {
                    $indexer->syncLesson($request->user(), $lesson);
                    $indexed++;
                } catch (RuntimeException) {
                    $skipped++;
                }
            }
        }

        return back()->with(
            'status',
            "Course knowledge indexed: {$indexed} lesson(s) indexed".($skipped ? ", {$skipped} skipped." : '.'),
        );
    }

    public function ask(Request $request, TeacherRagService $rag): View
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'min:5', 'max:3000'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
        ]);

        $teacher = $request->user();
        $courseId = isset($validated['course_id']) ? (int) $validated['course_id'] : null;

        if ($courseId !== null && ! $this->coursesFor($teacher->id)->contains('id', $courseId)) {
            abort(403);
        }

        try {
            $result = $rag->answer($teacher, $validated['question'], $courseId);
        } catch (RuntimeException $exception) {
            return view('teacher.rag.index', $this->pageData($request, null))
                ->withErrors(['rag' => $exception->getMessage()]);
        }

        return view('teacher.rag.index', $this->pageData($request, $result));
    }

    private function authorizeSource(Request $request, KnowledgeSource $source): void
    {
        if ((int) $source->owner_id !== (int) $request->user()->id) {
            abort(403);
        }
    }

    private function pageData(Request $request, ?array $result): array
    {
        return [
            'sources' => KnowledgeSource::query()
                ->with(['course'])
                ->withCount('chunks')
                ->where('owner_id', $request->user()->id)
                ->latest('updated_at')
                ->get(),
            'courses' => $this->coursesFor($request->user()->id),
            'result' => $result,
            'configured' => filled(config('ai.providers.openai.key')),
        ];
    }

    private function coursesFor(int $teacherId)
    {
        return Course::query()
            ->where('created_by', $teacherId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title']);
    }
}
