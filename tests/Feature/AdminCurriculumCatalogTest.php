<?php

namespace Tests\Feature;

use App\Models\AcademicClass;
use App\Models\Board;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCurriculumCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_open_curriculum_catalog(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('admin.curriculum.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_board_class_and_subject(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.curriculum.boards.store'), [
            'name' => 'Maharashtra State Board',
            'short_name' => 'MSBSHSE',
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $board = Board::where('name', 'Maharashtra State Board')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.curriculum.classes.store'), [
            'board_id' => $board->id,
            'name' => 'FYJC',
            'level' => '11',
            'sort_order' => 11,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $academicClass = AcademicClass::where('name', 'FYJC')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.curriculum.subjects.store'), [
            'academic_class_id' => $academicClass->id,
            'name' => 'Information Technology',
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('boards', [
            'id' => $board->id,
            'slug' => 'maharashtra-state-board',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('academic_classes', [
            'id' => $academicClass->id,
            'board_id' => $board->id,
            'slug' => 'fyjc',
        ]);
        $this->assertDatabaseHas('subjects', [
            'academic_class_id' => $academicClass->id,
            'name' => 'Information Technology',
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_update_each_curriculum_level(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Old Board', 'slug' => 'old-board']);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'Old Class',
            'slug' => 'old-class',
        ]);
        $subject = Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'Old Subject',
            'slug' => 'old-subject',
        ]);

        $this->actingAs($admin)->put(route('admin.curriculum.boards.update', $board), [
            'name' => 'Updated Board',
            'short_name' => 'UB',
            'sort_order' => 2,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(route('admin.curriculum.classes.update', $academicClass), [
            'board_id' => $board->id,
            'name' => 'SYJC',
            'level' => '12',
            'sort_order' => 12,
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->actingAs($admin)->put(route('admin.curriculum.subjects.update', $subject), [
            'academic_class_id' => $academicClass->id,
            'name' => 'Computer Science',
            'is_active' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertSame('Updated Board', $board->fresh()->name);
        $this->assertSame('SYJC', $academicClass->fresh()->name);
        $this->assertSame('Computer Science', $subject->fresh()->name);
    }

    public function test_admin_catalog_page_shows_hierarchy(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Maharashtra State Board', 'slug' => 'maharashtra-state-board']);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);
        Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'Information Technology',
            'slug' => 'information-technology',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.curriculum.index'))
            ->assertOk()
            ->assertSee('Maharashtra State Board')
            ->assertSee('FYJC')
            ->assertSee('Information Technology');
    }

    public function test_duplicate_class_name_is_blocked_within_same_board(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Board', 'slug' => 'board']);
        AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);

        $this->actingAs($admin)->post(route('admin.curriculum.classes.store'), [
            'board_id' => $board->id,
            'name' => 'FYJC',
            'is_active' => 1,
        ])->assertSessionHasErrors('name');
    }

    public function test_board_cannot_be_deleted_while_it_has_classes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Board', 'slug' => 'board']);
        AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.curriculum.boards.destroy', $board))
            ->assertSessionHasErrors('board');

        $this->assertDatabaseHas('boards', ['id' => $board->id]);
    }

    public function test_academic_class_cannot_be_deleted_while_it_has_subjects(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Board', 'slug' => 'board']);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);
        Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'IT',
            'slug' => 'it',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.curriculum.classes.destroy', $academicClass))
            ->assertSessionHasErrors('academic_class');

        $this->assertDatabaseHas('academic_classes', ['id' => $academicClass->id]);
    }

    public function test_subject_cannot_be_deleted_while_it_has_courses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Board', 'slug' => 'board']);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'FYJC',
            'slug' => 'fyjc',
        ]);
        $subject = Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'IT',
            'slug' => 'it',
        ]);
        Course::create([
            'subject_id' => $subject->id,
            'created_by' => $admin->id,
            'title' => 'FYJC IT',
            'slug' => 'fyjc-it',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.curriculum.subjects.destroy', $subject))
            ->assertSessionHasErrors('subject');

        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }

    public function test_empty_curriculum_records_can_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $board = Board::create(['name' => 'Empty Board', 'slug' => 'empty-board']);
        $academicClass = AcademicClass::create([
            'board_id' => $board->id,
            'name' => 'Empty Class',
            'slug' => 'empty-class',
        ]);
        $subject = Subject::create([
            'academic_class_id' => $academicClass->id,
            'created_by' => $admin->id,
            'name' => 'Empty Subject',
            'slug' => 'empty-subject',
        ]);

        $this->actingAs($admin)->delete(route('admin.curriculum.subjects.destroy', $subject));
        $this->actingAs($admin)->delete(route('admin.curriculum.classes.destroy', $academicClass));
        $this->actingAs($admin)->delete(route('admin.curriculum.boards.destroy', $board));

        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseMissing('academic_classes', ['id' => $academicClass->id]);
        $this->assertDatabaseMissing('boards', ['id' => $board->id]);
    }
}
