<?php

namespace App\Services\Admin;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CoursePublishingManager
{
    public function status(Course $course, ?array $checklist = null): array
    {
        $course->loadMissing([
            'units.lessons.contentBlocks',
            'units.lessons.practiceResources.assessment.questions',
            'vocabularyTerms',
        ]);

        $units = $course->units;
        $lessons = $units->flatMap->lessons;
        $blocks = $lessons->flatMap->contentBlocks;
        $activities = $lessons->flatMap->practiceResources;
        $assessments = $activities->map->assessment->filter()->values();
        $questions = $assessments->flatMap->questions;
        $vocabulary = $course->vocabularyTerms;

        $groups = collect([
            ['label' => 'Course', 'total' => 1, 'published' => $course->is_published ? 1 : 0],
            ['label' => 'Units', 'total' => $units->count(), 'published' => $units->where('is_published', true)->count()],
            ['label' => 'Lessons', 'total' => $lessons->count(), 'published' => $lessons->where('is_published', true)->count()],
            ['label' => 'Content blocks', 'total' => $blocks->count(), 'published' => $blocks->where('is_published', true)->count()],
            ['label' => 'Activities', 'total' => $activities->count(), 'published' => $activities->where('is_published', true)->count()],
            ['label' => 'Assessments', 'total' => $assessments->count(), 'published' => $assessments->where('is_published', true)->count()],
            ['label' => 'Questions', 'total' => $questions->count(), 'published' => $questions->where('is_published', true)->count()],
            ['label' => 'Vocabulary', 'total' => $vocabulary->count(), 'published' => $vocabulary->where('is_published', true)->count()],
        ]);

        $total = $groups->sum('total');
        $published = $groups->sum('published');
        $fullyPublished = $total > 0 && $published === $total;
        $hasPublishedContent = $published > 0;
        $ready = (bool) ($checklist['ready'] ?? false);

        if ($fullyPublished) {
            $key = 'published';
            $label = 'Published';
            $description = 'The course and all managed learning content are published.';
        } elseif ($hasPublishedContent) {
            $key = 'partial';
            $label = 'Partially published';
            $description = 'Some course content is published while other items are still draft.';
        } elseif ($ready) {
            $key = 'ready';
            $label = 'Ready to publish';
            $description = 'Required checklist items are complete and the course is still fully draft.';
        } else {
            $key = 'draft';
            $label = 'Draft';
            $description = 'The course is not published and still has required publishing work.';
        }

        return [
            'key' => $key,
            'label' => $label,
            'description' => $description,
            'total' => $total,
            'published' => $published,
            'draft' => max(0, $total - $published),
            'percentage' => $total > 0 ? (int) round(($published / $total) * 100) : 0,
            'groups' => $groups->all(),
            'fully_published' => $fullyPublished,
            'has_published_content' => $hasPublishedContent,
        ];
    }

    public function publishAll(Course $course): void
    {
        DB::transaction(function () use ($course): void {
            $course->update(['is_published' => true]);

            $unitIds = $course->units()->pluck('id');
            if ($unitIds->isEmpty()) {
                return;
            }

            $course->units()->update(['is_published' => true]);

            $lessonIds = \App\Models\Lesson::query()->whereIn('unit_id', $unitIds)->pluck('id');
            if ($lessonIds->isEmpty()) {
                return;
            }

            \App\Models\Lesson::query()->whereIn('id', $lessonIds)->update(['is_published' => true]);
            \App\Models\LessonContentBlock::query()->whereIn('lesson_id', $lessonIds)->update(['is_published' => true]);
            \App\Models\PracticeResource::query()->whereIn('lesson_id', $lessonIds)->update(['is_published' => true]);

            $practiceIds = \App\Models\PracticeResource::query()->whereIn('lesson_id', $lessonIds)->pluck('id');
            if ($practiceIds->isNotEmpty()) {
                \App\Models\Assessment::query()->whereIn('practice_resource_id', $practiceIds)->update(['is_published' => true]);
                $assessmentIds = \App\Models\Assessment::query()->whereIn('practice_resource_id', $practiceIds)->pluck('id');

                if ($assessmentIds->isNotEmpty()) {
                    \App\Models\AssessmentQuestion::query()->whereIn('assessment_id', $assessmentIds)->update(['is_published' => true]);
                }
            }

            $course->vocabularyTerms()->update(['is_published' => true]);
        });
    }

    public function unpublishAll(Course $course): void
    {
        DB::transaction(function () use ($course): void {
            $unitIds = $course->units()->pluck('id');
            $lessonIds = $unitIds->isEmpty()
                ? collect()
                : \App\Models\Lesson::query()->whereIn('unit_id', $unitIds)->pluck('id');

            if ($lessonIds->isNotEmpty()) {
                \App\Models\LessonContentBlock::query()->whereIn('lesson_id', $lessonIds)->update(['is_published' => false]);

                $practiceIds = \App\Models\PracticeResource::query()->whereIn('lesson_id', $lessonIds)->pluck('id');
                if ($practiceIds->isNotEmpty()) {
                    $assessmentIds = \App\Models\Assessment::query()->whereIn('practice_resource_id', $practiceIds)->pluck('id');
                    if ($assessmentIds->isNotEmpty()) {
                        \App\Models\AssessmentQuestion::query()->whereIn('assessment_id', $assessmentIds)->update(['is_published' => false]);
                        \App\Models\Assessment::query()->whereIn('id', $assessmentIds)->update(['is_published' => false]);
                    }

                    \App\Models\PracticeResource::query()->whereIn('id', $practiceIds)->update(['is_published' => false]);
                }

                \App\Models\Lesson::query()->whereIn('id', $lessonIds)->update(['is_published' => false]);
            }

            if ($unitIds->isNotEmpty()) {
                $course->units()->update(['is_published' => false]);
            }

            $course->vocabularyTerms()->update(['is_published' => false]);
            $course->update(['is_published' => false]);
        });
    }
}
