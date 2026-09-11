<?php

namespace Database\Seeders;

use App\Models\AcademicClass;
use App\Models\Board;
use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $board = Board::updateOrCreate(
            ['slug' => 'maharashtra-state-board'],
            [
                'name' => 'Maharashtra State Board',
                'short_name' => 'MSBSHSE',
                'description' => 'Maharashtra State Board curriculum for SignGyaan.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $academicClass = AcademicClass::updateOrCreate(
            ['board_id' => $board->id, 'slug' => 'fyjc'],
            [
                'name' => 'FYJC',
                'level' => '11',
                'description' => 'First Year Junior College',
                'is_active' => true,
                'sort_order' => 11,
            ]
        );

        $creator = User::first();

        if (! $creator) {
            return;
        }

        $subject = Subject::updateOrCreate(
            ['slug' => 'information-technology'],
            [
                'academic_class_id' => $academicClass->id,
                'created_by' => $creator->id,
                'name' => 'Information Technology',
                'description' => 'FYJC Information Technology curriculum.',
                'is_active' => true,
            ]
        );

        $course = Course::updateOrCreate(
            ['slug' => 'fyjc-information-technology'],
            [
                'subject_id' => $subject->id,
                'created_by' => $creator->id,
                'title' => 'FYJC Information Technology',
                'level' => 'FYJC',
                'description' => 'Maharashtra State Board FYJC Information Technology.',
                'is_active' => true,
            ]
        );

        $unit = CourseUnit::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Chapter 1 - Basics of IT'],
            [
                'description' => 'Introduction to Information Technology fundamentals.',
                'position' => 1,
                'is_active' => true,
            ]
        );

        Lesson::updateOrCreate(
            ['course_unit_id' => $unit->id, 'title' => 'Introduction to IT'],
            [
                'summary' => 'Introduction to Information Technology and its basic concepts.',
                'notes' => 'Starter lesson for the Maharashtra Board FYJC Information Technology curriculum.',
                'estimated_minutes' => 20,
                'position' => 1,
                'status' => 'draft',
                'published_at' => null,
            ]
        );
    }
}
