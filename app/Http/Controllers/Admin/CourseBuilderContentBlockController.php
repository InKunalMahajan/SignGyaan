<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContentBlock;
use App\Models\MediaAsset;
use App\Models\PracticeResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseBuilderContentBlockController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->ensureLessonBelongsToCourse($course, $lesson);
        $validated = $this->validateBlock($request, $lesson);

        DB::transaction(function () use ($request, $lesson, $validated) {
            $validated['lesson_id'] = $lesson->id;
            $validated['sort_order'] = ((int) $lesson->contentBlocks()->max('sort_order')) + 1;
            $validated['is_published'] = $request->boolean('is_published');

            LessonContentBlock::create($validated);
        });

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-lesson-'.$lesson->id)
            ->with('status', 'Lesson content block added successfully.');
    }

    public function update(Request $request, Course $course, Lesson $lesson, LessonContentBlock $contentBlock): RedirectResponse
    {
        $this->ensureLessonBelongsToCourse($course, $lesson);
        $this->ensureBlockBelongsToLesson($lesson, $contentBlock);

        $validated = $this->validateBlock($request, $lesson);
        $validated['is_published'] = $request->boolean('is_published');
        $contentBlock->update($validated);

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-block-'.$contentBlock->id)
            ->with('status', 'Lesson content block updated successfully.');
    }

    public function destroy(Course $course, Lesson $lesson, LessonContentBlock $contentBlock): RedirectResponse
    {
        $this->ensureLessonBelongsToCourse($course, $lesson);
        $this->ensureBlockBelongsToLesson($lesson, $contentBlock);
        $contentBlock->delete();

        return redirect()
            ->to(route('admin.courses.builder', $course).'#builder-lesson-'.$lesson->id)
            ->with('status', 'Lesson content block deleted successfully.');
    }

    private function validateBlock(Request $request, Lesson $lesson): array
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(LessonContentBlock::TYPES)],
            'title' => ['nullable', 'string', 'max:180'],
            'body' => ['nullable', 'string', 'max:100000'],
            'media_asset_id' => ['nullable', 'integer', Rule::exists('media_assets', 'id')],
            'practice_resource_id' => ['nullable', 'integer', Rule::exists('practice_resources', 'id')],
        ]);

        $type = $validated['type'];
        $bodyTypes = ['text', 'key_points', 'example', 'transcript', 'vocabulary'];

        if (in_array($type, $bodyTypes, true) && blank($validated['body'] ?? null)) {
            throw ValidationException::withMessages([
                'body' => 'This content block needs text content.',
            ]);
        }

        if (in_array($type, ['image', 'isl_video'], true)) {
            $media = isset($validated['media_asset_id'])
                ? MediaAsset::query()->find($validated['media_asset_id'])
                : null;

            if (! $media) {
                throw ValidationException::withMessages([
                    'media_asset_id' => 'Choose a Media Library item for this block.',
                ]);
            }

            if ($type === 'image' && $media->media_type !== 'image') {
                throw ValidationException::withMessages([
                    'media_asset_id' => 'Image blocks require an image Media Library item.',
                ]);
            }

            if ($type === 'isl_video' && $media->media_type !== 'video') {
                throw ValidationException::withMessages([
                    'media_asset_id' => 'ISL video blocks require a video Media Library item.',
                ]);
            }
        } else {
            $validated['media_asset_id'] = null;
        }

        if (in_array($type, ['practice', 'resource'], true)) {
            $practice = isset($validated['practice_resource_id'])
                ? PracticeResource::query()
                    ->whereKey($validated['practice_resource_id'])
                    ->where('lesson_id', $lesson->id)
                    ->first()
                : null;

            if (! $practice) {
                throw ValidationException::withMessages([
                    'practice_resource_id' => 'Choose an activity from this lesson.',
                ]);
            }

            if ($practice->kind !== $type) {
                throw ValidationException::withMessages([
                    'practice_resource_id' => 'The selected activity does not match the chosen block type.',
                ]);
            }
        } else {
            $validated['practice_resource_id'] = null;
        }

        return $validated;
    }

    private function ensureLessonBelongsToCourse(Course $course, Lesson $lesson): void
    {
        $belongs = $lesson->unit()
            ->where('course_id', $course->id)
            ->exists();

        abort_unless($belongs, 404);
    }

    private function ensureBlockBelongsToLesson(Lesson $lesson, LessonContentBlock $contentBlock): void
    {
        abort_unless((int) $contentBlock->lesson_id === (int) $lesson->id, 404);
    }
}
