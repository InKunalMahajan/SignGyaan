<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\Admin\CoursePublishingChecklist;
use Illuminate\View\View;

class CoursePublishingChecklistController extends Controller
{
    public function __invoke(Course $course, CoursePublishingChecklist $checklist): View
    {
        return view('admin.courses.publishing-checklist', [
            'course' => $course->load('subject'),
            'checklist' => $checklist->evaluate($course),
        ]);
    }
}
