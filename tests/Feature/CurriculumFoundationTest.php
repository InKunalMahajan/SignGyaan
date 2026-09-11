<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\Board;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\CurriculumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_academic_class_subject_relationships_work(): void
    {
        $user = User::factory()->create();
        $board = Board::create([
            'name' => 'Maharashtra State Board',
            'slug' => 'maharashtra-state-board',
        ]);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);
        $subject = Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $user->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology',
            'is_active' => true,
        ]);

        $this->assertTrue($board->academicClasses->contains($academicClass));
        $this->assertTrue($academicClass->subjects->contains($subject));
        $this->assertTrue($subject->academicClass->is($academicClass));
        $this->assertTrue($subject->academicClass->board->is($board));
    }

    public function test_curriculum_seeder_builds_the_fyjc_information_technology_path(): void
    {
        User::factory()->create();

        $this->seed(CurriculumSeeder::class);

        $course = Course::query()
            ->with('subject.academicClass.board', 'units.lessons')
            ->where('slug', 'fyjc-information-technology')
            ->firstOrFail();

        $this->assertSame('Information Technology', $course->subject->name);
        $this->assertSame('FYJC', $course->subject->academicClass->name);
        $this->assertSame('Maharashtra State Board', $course->subject->academicClass->board->name);
        $this->assertSame('Chapter 1 - Basics of IT', $course->units->first()->title);
        $this->assertSame('Introduction to IT', $course->units->first()->lessons->first()->title);
    }
}
