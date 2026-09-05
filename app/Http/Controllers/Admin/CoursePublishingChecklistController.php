<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Admin\CoursePublishingChecklist;
use App\Services\Admin\CoursePublishingManager;
use Illuminate\View\View;

class CoursePublishingChecklistController extends Controller
{
    public function __invoke(
        Course $course,
        CoursePublishingChecklist $checklist,
        CoursePublishingManager $publishing
    ): View {
        $result = $checklist->evaluate($course);

        return view('admin.courses.publishing-checklist', [
            'course' => $course->load('subject'),
            'checklist' => $result,
            'publishingStatus' => $publishing->status($course, $result),
        ]);
    }
}
