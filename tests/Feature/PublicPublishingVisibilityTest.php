<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\MediaAsset;
use App\Models\PracticeResource;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPublishingVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_lesson_uses_stable_canonical_navigation_and_legacy_links_redirect(): void
    {
        [$subject, $course, $unit, $lesson] = $this->createPublishedHierarchy();

        $stableUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]);

        $this->get($stableUrl)
            ->assertOk()
            ->assertSee($lesson->title)
            ->assertSee($unit->title);

        $legacyUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'unit-1-lesson-1',
        ]);

        $this->get($legacyUrl)
            ->assertStatus(301)
            ->assertRedirect($stableUrl);
    }

    public function test_draft_units_and_lessons_are_hidden_from_course_navigation_and_direct_access(): void
    {
        [$subject, $course, $publishedUnit, $publishedLesson] = $this->createPublishedHierarchy();

        $draftLesson = Lesson::create([
            'unit_id' => $publishedUnit->id,
            'title' => 'Private Draft Lesson',
            'slug' => 'private-draft-lesson',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $draftUnit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Private Draft Unit',
            'slug' => 'private-draft-unit',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $hiddenByParent = Lesson::create([
            'unit_id' => $draftUnit->id,
            'title' => 'Hidden By Draft Unit',
            'slug' => 'hidden-by-draft-unit',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $courseUrl = route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
        ]);

        $this->get($courseUrl)
            ->assertOk()
            ->assertSee($publishedLesson->title)
            ->assertDontSee($draftLesson->title)
            ->assertDontSee($draftUnit->title)
            ->assertDontSee($hiddenByParent->title);

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$draftLesson->id,
        ]))->assertNotFound();

        $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$hiddenByParent->id,
        ]))->assertNotFound();

        $this->get(route('search', ['q' => 'Private']))
            ->assertOk()
            ->assertDontSee($draftLesson->title);
    }

    public function test_draft_subjects_and_courses_cannot_be_opened_publicly(): void
    {
        $draftSubject = Subject::create([
            'name' => 'Private Subject',
            'slug' => 'private-subject',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $courseUnderDraftSubject = Course::create([
            'subject_id' => $draftSubject->id,
            'title' => 'Published Child Course',
            'slug' => 'published-child-course',
            'is_published' => true,
        ]);

        $this->get(route('subjects.show', $draftSubject->slug))->assertNotFound();
        $this->get(route('courses.show', [
            'subject' => $draftSubject->slug,
            'course' => $courseUnderDraftSubject->slug,
        ]))->assertNotFound();

        $publicSubject = Subject::create([
            'name' => 'Public Subject',
            'slug' => 'public-subject',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $draftCourse = Course::create([
            'subject_id' => $publicSubject->id,
            'title' => 'Private Course',
            'slug' => 'private-course',
            'is_published' => false,
        ]);

        $this->get(route('courses.show', [
            'subject' => $publicSubject->slug,
            'course' => $draftCourse->slug,
        ]))->assertNotFound();
    }

    public function test_draft_practice_and_media_assets_do_not_leak_into_published_lessons(): void
    {
        [$subject, $course, $unit, $lesson] = $this->createPublishedHierarchy();

        $draftPractice = PracticeResource::create([
            'lesson_id' => $lesson->id,
            'title' => 'Private Teacher Worksheet',
            'slug' => 'private-teacher-worksheet',
            'kind' => 'resource',
            'resource_type' => 'worksheet',
            'content' => 'This draft content must not be public.',
            'sort_order' => 1,
            'is_published' => false,
        ]);

        $draftMedia = MediaAsset::create([
            'title' => 'Private ISL Video',
            'media_type' => 'video',
            'source' => 'external',
            'external_url' => 'https://example.com/private-isl-video.mp4',
            'is_published' => false,
        ]);

        $lesson->update(['isl_media_asset_id' => $draftMedia->id]);

        $response = $this->get(route('courses.show', [
            'subject' => $subject->slug,
            'course' => $course->slug,
            'lesson' => 'lesson-'.$lesson->id,
        ]));

        $response
            ->assertOk()
            ->assertDontSee($draftPractice->title)
            ->assertDontSee($draftPractice->content)
            ->assertDontSee($draftMedia->title)
            ->assertDontSee($draftMedia->external_url);
    }

    private function createPublishedHierarchy(): array
    {
        $subject = Subject::create([
            'name' => 'Test Subject',
            'slug' => 'test-subject',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $course = Course::create([
            'subject_id' => $subject->id,
            'title' => 'Test Course',
            'slug' => 'test-course',
            'level' => 'Beginner',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $unit = Unit::create([
            'course_id' => $course->id,
            'title' => 'Test Unit',
            'slug' => 'test-unit',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'unit_id' => $unit->id,
            'title' => 'Test Published Lesson',
            'slug' => 'test-published-lesson',
            'short_description' => 'A published lesson used for visibility testing.',
            'content' => 'Published lesson content.',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        return [$subject, $course, $unit, $lesson];
    }
}
