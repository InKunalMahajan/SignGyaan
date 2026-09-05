<?php

namespace App\Services\Admin;

use App\Models\Course;
use Illuminate\Support\Collection;

class CoursePublishingChecklist
{
    public function evaluate(Course $course): array
    {
        $course->loadMissing([
            'subject',
            'units.lessons.mediaAsset',
            'units.lessons.contentBlocks.mediaAsset',
            'units.lessons.practiceResources.mediaAsset',
            'units.lessons.practiceResources.assessment.questions',
        ]);

        $units = $course->units;
        $lessons = $units->flatMap->lessons;
        $blocks = $lessons->flatMap->contentBlocks;
        $activities = $lessons->flatMap->practiceResources;
        $assessments = $activities->map->assessment->filter()->values();

        $emptyUnits = $units->filter(fn ($unit) => $unit->lessons->isEmpty());
        $lessonsWithoutContent = $lessons->filter(function ($lesson) {
            return blank($lesson->content)
                && blank($lesson->simplified_summary)
                && blank($lesson->learning_objectives)
                && blank($lesson->key_points)
                && blank($lesson->example_content)
                && $lesson->contentBlocks->isEmpty();
        });

        $imageBlocksMissingAlt = $blocks->filter(function ($block) {
            return $block->type === 'image'
                && $block->mediaAsset
                && blank($block->mediaAsset->alt_text);
        });

        $unpublishedMediaReferences = collect();
        foreach ($lessons as $lesson) {
            if ($lesson->mediaAsset && ! $lesson->mediaAsset->is_published) {
                $unpublishedMediaReferences->push($lesson->mediaAsset->id);
            }

            foreach ($lesson->contentBlocks as $block) {
                if ($block->mediaAsset && ! $block->mediaAsset->is_published) {
                    $unpublishedMediaReferences->push($block->mediaAsset->id);
                }
            }

            foreach ($lesson->practiceResources as $activity) {
                if ($activity->mediaAsset && ! $activity->mediaAsset->is_published) {
                    $unpublishedMediaReferences->push($activity->mediaAsset->id);
                }
            }
        }
        $unpublishedMediaReferences = $unpublishedMediaReferences->unique()->values();

        $assessmentsWithoutQuestions = $assessments->filter(function ($assessment) {
            return $assessment->questions->where('is_published', true)->isEmpty();
        });

        $lessonsWithoutTextSupport = $lessons->filter(function ($lesson) {
            $hasTranscriptBlock = $lesson->contentBlocks->contains(fn ($block) => $block->type === 'transcript' && filled($block->body));

            return blank($lesson->simplified_summary)
                && blank($lesson->isl_transcript)
                && blank($lesson->content)
                && ! $hasTranscriptBlock;
        });

        $lessonsWithoutIslSupport = $lessons->filter(function ($lesson) {
            $hasIslBlock = $lesson->contentBlocks->contains(fn ($block) => $block->type === 'isl_video' && $block->mediaAsset);

            return blank($lesson->isl_video_url)
                && ! ($lesson->mediaAsset && $lesson->mediaAsset->media_type === 'video')
                && ! $hasIslBlock
                && blank($lesson->isl_transcript);
        });

        $lessonsWithoutDuration = $lessons->filter(fn ($lesson) => ! $lesson->estimated_duration_minutes);

        $checks = collect([
            $this->check(
                'subject-published',
                'Subject is published',
                'The course can only appear publicly when its subject is published.',
                (bool) $course->subject?->is_published,
                'required',
                $course->subject?->is_published ? null : 'Publish the subject before publishing this course.'
            ),
            $this->check(
                'course-description',
                'Course description is ready',
                'Learners should understand what the course covers before they start.',
                filled($course->short_description) || filled($course->description),
                'required',
                filled($course->short_description) || filled($course->description) ? null : 'Add a short description or full description.'
            ),
            $this->check(
                'course-structure',
                'Course has units and lessons',
                'A publishable course needs at least one unit and one lesson.',
                $units->isNotEmpty() && $lessons->isNotEmpty(),
                'required',
                $units->isNotEmpty() && $lessons->isNotEmpty() ? null : 'Add at least one unit and one lesson.'
            ),
            $this->check(
                'unit-lessons',
                'Every unit contains a lesson',
                'Empty units create dead ends in the learner curriculum.',
                $emptyUnits->isEmpty(),
                'required',
                $emptyUnits->isEmpty() ? null : $this->countMessage($emptyUnits, 'unit has no lessons', 'units have no lessons')
            ),
            $this->check(
                'lesson-content',
                'Every lesson has learning content',
                'Each lesson needs legacy lesson text or at least one rich content block.',
                $lessons->isNotEmpty() && $lessonsWithoutContent->isEmpty(),
                'required',
                $lessonsWithoutContent->isEmpty() ? null : $this->countMessage($lessonsWithoutContent, 'lesson needs content', 'lessons need content')
            ),
            $this->check(
                'image-alt-text',
                'Images have alternative text',
                'Alternative text supports learners who cannot access the image visually.',
                $imageBlocksMissingAlt->isEmpty(),
                'required',
                $imageBlocksMissingAlt->isEmpty() ? null : $this->countMessage($imageBlocksMissingAlt, 'image needs alt text', 'images need alt text')
            ),
            $this->check(
                'media-published',
                'Linked media is published',
                'Draft media linked from course content will not be available to learners.',
                $unpublishedMediaReferences->isEmpty(),
                'required',
                $unpublishedMediaReferences->isEmpty() ? null : $this->countMessage($unpublishedMediaReferences, 'linked media item is still draft', 'linked media items are still draft')
            ),
            $this->check(
                'assessment-questions',
                'Assessments have published questions',
                'Any assessment attached to this course should contain at least one published question.',
                $assessmentsWithoutQuestions->isEmpty(),
                'required',
                $assessmentsWithoutQuestions->isEmpty() ? null : $this->countMessage($assessmentsWithoutQuestions, 'assessment needs a published question', 'assessments need published questions')
            ),
            $this->check(
                'text-support',
                'Lessons include clear text support',
                'A simplified summary, transcript or lesson text improves accessibility and review.',
                $lessons->isNotEmpty() && $lessonsWithoutTextSupport->isEmpty(),
                'recommended',
                $lessonsWithoutTextSupport->isEmpty() ? null : $this->countMessage($lessonsWithoutTextSupport, 'lesson could use text support', 'lessons could use text support')
            ),
            $this->check(
                'isl-support',
                'Lessons include ISL support where appropriate',
                'ISL video or transcript support is strongly encouraged for SignGyaan lessons.',
                $lessons->isNotEmpty() && $lessonsWithoutIslSupport->isEmpty(),
                'recommended',
                $lessonsWithoutIslSupport->isEmpty() ? null : $this->countMessage($lessonsWithoutIslSupport, 'lesson has no ISL support yet', 'lessons have no ISL support yet')
            ),
            $this->check(
                'lesson-duration',
                'Lesson duration is estimated',
                'Estimated time helps learners plan their study session.',
                $lessons->isNotEmpty() && $lessonsWithoutDuration->isEmpty(),
                'recommended',
                $lessonsWithoutDuration->isEmpty() ? null : $this->countMessage($lessonsWithoutDuration, 'lesson has no duration', 'lessons have no duration')
            ),
        ]);

        $required = $checks->where('level', 'required')->values();
        $recommended = $checks->where('level', 'recommended')->values();
        $blockers = $required->where('passed', false)->values();
        $warnings = $recommended->where('passed', false)->values();
        $passed = $checks->where('passed', true)->count();

        return [
            'checks' => $checks->all(),
            'required' => $required->all(),
            'recommended' => $recommended->all(),
            'blockers' => $blockers->all(),
            'warnings' => $warnings->all(),
            'ready' => $blockers->isEmpty(),
            'passed_count' => $passed,
            'total_count' => $checks->count(),
            'required_passed' => $required->where('passed', true)->count(),
            'required_total' => $required->count(),
            'warning_count' => $warnings->count(),
        ];
    }

    private function check(string $key, string $title, string $description, bool $passed, string $level, ?string $detail = null): array
    {
        return compact('key', 'title', 'description', 'passed', 'level', 'detail');
    }

    private function countMessage(Collection $items, string $singular, string $plural): string
    {
        $count = $items->count();

        return $count.' '.($count === 1 ? $singular : $plural).'.';
    }
}
