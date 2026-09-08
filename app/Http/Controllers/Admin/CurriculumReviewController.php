<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Notifications\ReviewWorkflowNotification;
use App\Support\ReviewAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurriculumReviewController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate(['q'=>['nullable','string','max:120'],'review'=>['nullable',Rule::in(Lesson::REVIEW_STATUSES)]]);
        $query = Course::query()->with(['subject','creator'])->withCount('units');

        if (! empty($validated['q'])) {
            $term = $validated['q'];
            $query->where(function ($courseQuery) use ($term) {
                $courseQuery->where('title','like',"%{$term}%")
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->where('name','like',"%{$term}%"))
                    ->orWhereHas('creator', fn ($creatorQuery) => $creatorQuery->where('name','like',"%{$term}%"));
            });
        }

        if (! empty($validated['review'])) {
            $review = $validated['review'];
            $query->whereHas('units.lessons', fn ($lessonQuery) => $lessonQuery->where('review_status', $review));
        }

        return view('admin.curriculum.index', [
            'courses'=>$query->orderBy('title')->paginate(15)->withQueryString(),
            'reviewCounts'=>Lesson::query()->selectRaw('review_status, COUNT(*) as total')->groupBy('review_status')->pluck('total','review_status'),
            'subjectCount'=>Subject::count(),'courseCount'=>Course::count(),'lessonCount'=>Lesson::count(),
        ]);
    }

    public function show(Course $course): View
    {
        $course->load(['subject.creator','creator','classes.teacher','units'=>fn($query)=>$query->with(['lessons'=>fn($lessonQuery)=>$lessonQuery->with('reviewer')->orderBy('position')->orderBy('id')])->orderBy('position')->orderBy('id')]);
        $lessons = $course->units->flatMap(fn ($unit) => $unit->lessons);
        return view('admin.curriculum.show', ['course'=>$course,'lessons'=>$lessons,'pendingCount'=>$lessons->where('review_status','pending')->count(),'approvedCount'=>$lessons->where('review_status','approved')->count(),'changesRequestedCount'=>$lessons->where('review_status','changes_requested')->count()]);
    }

    public function toggleSubject(Subject $subject): RedirectResponse
    {
        $subject->update(['is_active'=>!$subject->is_active]);
        return back()->with('status',$subject->is_active?'Subject activated.':'Subject deactivated.');
    }

    public function toggleCourse(Course $course): RedirectResponse
    {
        $course->update(['is_active'=>!$course->is_active]);
        return back()->with('status',$course->is_active?'Course activated.':'Course deactivated.');
    }

    public function reviewLesson(Request $request, Lesson $lesson, ReviewAuditLogger $audit): RedirectResponse
    {
        $validated = $request->validate(['review_status'=>['required',Rule::in(Lesson::REVIEW_STATUSES)],'review_notes'=>['nullable','string','max:5000']]);
        if ($validated['review_status']==='changes_requested' && blank($validated['review_notes'] ?? null)) {
            return back()->withErrors(['review_notes'=>'Please explain what the Teacher should change.']);
        }

        $previousReviewStatus = $lesson->review_status;
        $lesson->update(['review_status'=>$validated['review_status'],'review_notes'=>$validated['review_notes'] ?? null,'reviewed_by'=>$request->user()->id,'reviewed_at'=>now()]);

        $eventType = match ($lesson->review_status) {
            'approved' => 'review_approved',
            'changes_requested' => 'changes_requested',
            default => 'review_returned_pending',
        };

        $audit->record($lesson, $request->user(), $eventType, $previousReviewStatus, $lesson->review_status, [
            'review_notes' => $validated['review_notes'] ?? null,
            'metadata' => ['source' => 'admin_review_action'],
        ]);

        $lesson->loadMissing('unit.course.creator');
        $teacher = $lesson->unit?->course?->creator;

        if ($teacher && $teacher->is_active && in_array($lesson->review_status, ['approved','changes_requested'], true)) {
            $teacher->notify(new ReviewWorkflowNotification(
                $lesson->review_status === 'approved' ? 'review_approved' : 'changes_requested',
                $lesson->review_status === 'approved' ? 'Lesson approved' : 'Changes requested',
                $lesson->review_status === 'approved'
                    ? 'Admin approved “'.$lesson->title.'” for learner access.'
                    : 'Admin requested changes to “'.$lesson->title.'”. Review the feedback and resubmit when ready.',
                route('teacher.reviews.index', ['review'=>$lesson->review_status]),
                $lesson->id
            ));
        }

        $message = match ($lesson->review_status) { 'approved'=>'Lesson approved for learner access.','changes_requested'=>'Changes requested from the Teacher.',default=>'Lesson returned to pending review.' };
        return back()->with('status',$message);
    }
}
