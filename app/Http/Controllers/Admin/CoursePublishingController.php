<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Admin\CoursePublishingChecklist;
use App\Services\Admin\CoursePublishingManager;
use Illuminate\Http\RedirectResponse;

class CoursePublishingController extends Controller
{
    public function publish(
        Course $course,
        CoursePublishingChecklist $checklist,
        CoursePublishingManager $publishing
    ): RedirectResponse {
        $result = $checklist->evaluate($course);

        if (! $result['ready']) {
            return redirect()
                ->route('admin.courses.publishing-checklist', $course)
                ->withErrors([
                    'publishing' => 'This course still has required publishing blockers. Resolve them before publishing.',
                ]);
        }

        $publishing->publishAll($course);

        return redirect()
            ->route('admin.courses.publishing-checklist', $course)
            ->with('status', 'Course and managed learning content published successfully. Shared media and the subject were not changed.');
    }

    public function unpublish(Course $course, CoursePublishingManager $publishing): RedirectResponse
    {
        $publishing->unpublishAll($course);

        return redirect()
            ->route('admin.courses.publishing-checklist', $course)
            ->with('status', 'Course and managed learning content moved to draft. Shared media and the subject were not changed.');
    }
}
