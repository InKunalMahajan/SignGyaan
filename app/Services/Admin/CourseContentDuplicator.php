<?php

namespace App\Services\Admin;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\PracticeResource;
use App\Models\Unit;
use App\Models\VocabularyTerm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseContentDuplicator
{
    public function duplicateLesson(Lesson $lesson, ?Unit $targetUnit = null): Lesson
    {
        $targetUnit ??= $lesson->unit;

        return DB::transaction(fn () => $this->cloneLesson($lesson, $targetUnit, [], true));
    }

    public function duplicateUnit(Unit $unit, ?Course $targetCourse = null): Unit
    {
        $targetCourse ??= $unit->course;

        return DB::transaction(fn () => $this->cloneUnit($unit, $targetCourse, [], true));
    }

    public function duplicateCourse(Course $course): Course
    {
        return DB::transaction(function () use ($course) {
            $course->load([
                'vocabularyTerms',
                'units.lessons.vocabularyTerms',
                'units.lessons.practiceResources.assessment.questions.options',
                'units.lessons.contentBlocks',
            ]);

            $copy = $course->replicate();
            $copy->title = $this->copyTitle($course->title);
            $copy->slug = $this->uniqueCourseSlug($course->subject_id, Str::slug($copy->title) ?: 'course-copy');
            $copy->sort_order = ((int) Course::query()->where('subject_id', $course->subject_id)->max('sort_order')) + 1;
            $copy->is_featured = false;
            $copy->is_published = false;
            $copy->save();

            $vocabularyMap = [];
            foreach ($course->vocabularyTerms as $term) {
                $termCopy = $term->replicate();
                $termCopy->course_id = $copy->id;
                $termCopy->slug = $this->uniqueVocabularySlug(Str::slug($term->term.' copy') ?: 'term-copy');
                $termCopy->is_published = false;
                $termCopy->save();
                $vocabularyMap[$term->id] = $termCopy->id;
            }

            foreach ($course->units as $unit) {
                $this->cloneUnit($unit, $copy, $vocabularyMap, false);
            }

            return $copy;
        });
    }

    private function cloneUnit(Unit $unit, Course $targetCourse, array $vocabularyMap = [], bool $markAsCopy = true): Unit
    {
        $unit->loadMissing([
            'lessons.vocabularyTerms',
            'lessons.practiceResources.assessment.questions.options',
            'lessons.contentBlocks',
        ]);

        $copy = $unit->replicate();
        $copy->course_id = $targetCourse->id;
        $copy->title = $markAsCopy ? $this->copyTitle($unit->title) : $unit->title;
        $copy->slug = $this->uniqueUnitSlug($targetCourse->id, Str::slug($copy->title) ?: 'unit-copy');
        $copy->sort_order = ((int) Unit::query()->where('course_id', $targetCourse->id)->max('sort_order')) + 1;
        $copy->is_published = false;
        $copy->save();

        foreach ($unit->lessons as $lesson) {
            $this->cloneLesson($lesson, $copy, $vocabularyMap, false);
        }

        return $copy;
    }

    private function cloneLesson(Lesson $lesson, Unit $targetUnit, array $vocabularyMap = [], bool $markAsCopy = true): Lesson
    {
        $lesson->loadMissing([
            'vocabularyTerms',
            'practiceResources.assessment.questions.options',
            'contentBlocks',
        ]);

        $copy = $lesson->replicate();
        $copy->unit_id = $targetUnit->id;
        $copy->title = $markAsCopy ? $this->copyTitle($lesson->title) : $lesson->title;
        $copy->slug = $this->uniqueLessonSlug($targetUnit->id, Str::slug($copy->title) ?: 'lesson-copy');
        $copy->sort_order = ((int) Lesson::query()->where('unit_id', $targetUnit->id)->max('sort_order')) + 1;
        $copy->is_published = false;
        $copy->save();

        $practiceMap = [];
        foreach ($lesson->practiceResources as $practice) {
            $practiceCopy = $practice->replicate();
            $practiceCopy->lesson_id = $copy->id;
            $practiceCopy->slug = $this->uniquePracticeSlug($copy->id, Str::slug($practice->title) ?: 'activity');
            $practiceCopy->is_published = false;
            $practiceCopy->save();
            $practiceMap[$practice->id] = $practiceCopy->id;

            if ($practice->assessment) {
                $this->cloneAssessment($practice->assessment, $practiceCopy);
            }
        }

        foreach ($lesson->contentBlocks as $block) {
            $blockCopy = $block->replicate();
            $blockCopy->lesson_id = $copy->id;
            $blockCopy->practice_resource_id = $block->practice_resource_id
                ? ($practiceMap[$block->practice_resource_id] ?? null)
                : null;
            $blockCopy->is_published = false;
            $blockCopy->save();
        }

        $sync = [];
        foreach ($lesson->vocabularyTerms as $term) {
            $termId = $vocabularyMap[$term->id] ?? $term->id;
            $sync[$termId] = ['sort_order' => (int) ($term->pivot->sort_order ?? 0)];
        }
        $copy->vocabularyTerms()->sync($sync);

        return $copy;
    }

    private function cloneAssessment(Assessment $assessment, PracticeResource $practiceCopy): void
    {
        $assessment->loadMissing('questions.options');

        $assessmentCopy = $assessment->replicate();
        $assessmentCopy->practice_resource_id = $practiceCopy->id;
        $assessmentCopy->is_published = false;
        $assessmentCopy->save();

        foreach ($assessment->questions as $question) {
            $questionCopy = $question->replicate();
            $questionCopy->assessment_id = $assessmentCopy->id;
            $questionCopy->is_published = false;
            $questionCopy->save();

            foreach ($question->options as $option) {
                $optionCopy = $option->replicate();
                $optionCopy->assessment_question_id = $questionCopy->id;
                $optionCopy->save();
            }
        }
    }

    private function copyTitle(string $title): string
    {
        return Str::limit('Copy of '.$title, 180, '');
    }

    private function uniqueCourseSlug(int $subjectId, string $base): string
    {
        return $this->uniqueSlug($base, 180, fn (string $slug) => Course::query()->where('subject_id', $subjectId)->where('slug', $slug)->exists());
    }

    private function uniqueUnitSlug(int $courseId, string $base): string
    {
        return $this->uniqueSlug($base, 200, fn (string $slug) => Unit::query()->where('course_id', $courseId)->where('slug', $slug)->exists());
    }

    private function uniqueLessonSlug(int $unitId, string $base): string
    {
        return $this->uniqueSlug($base, 200, fn (string $slug) => Lesson::query()->where('unit_id', $unitId)->where('slug', $slug)->exists());
    }

    private function uniquePracticeSlug(int $lessonId, string $base): string
    {
        return $this->uniqueSlug($base, 200, fn (string $slug) => PracticeResource::query()->where('lesson_id', $lessonId)->where('slug', $slug)->exists());
    }

    private function uniqueVocabularySlug(string $base): string
    {
        return $this->uniqueSlug($base, 200, fn (string $slug) => VocabularyTerm::query()->where('slug', $slug)->exists());
    }

    private function uniqueSlug(string $base, int $maxLength, callable $exists): string
    {
        $base = Str::limit(trim($base, '-'), $maxLength - 10, '');
        $slug = $base !== '' ? $base : 'copy';
        $candidate = $slug;
        $suffix = 2;

        while ($exists($candidate)) {
            $suffixText = '-'.$suffix++;
            $candidate = Str::limit($slug, $maxLength - strlen($suffixText), '').$suffixText;
        }

        return $candidate;
    }
}
