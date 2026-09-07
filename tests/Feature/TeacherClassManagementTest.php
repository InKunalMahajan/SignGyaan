<?php

namespace Tests\Feature;

use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherClassManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_teacher_cannot_open_teacher_workspace(): void
    {
        $learner = User::factory()->create(['role' => 'learner']);

        $this->actingAs($learner)
            ->get(route('teacher.classes.index'))
            ->assertForbidden();
    }

    public function test_teacher_profile_is_created_and_can_be_updated(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->get(route('teacher.profile.show'))
            ->assertOk();

        $this->assertDatabaseHas('teacher_profiles', [
            'user_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.profile.update'), [
                'name' => 'Kunal Teacher',
                'email' => 'teacher@example.com',
                'phone' => '9999999999',
                'designation' => 'Assistant Lecturer',
                'qualification' => 'B.Com',
                'preferred_language' => 'english',
                'communication_mode' => 'both',
                'bio' => 'Accessible learning teacher.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teacher_profiles', [
            'user_id' => $teacher->id,
            'designation' => 'Assistant Lecturer',
            'communication_mode' => 'both',
        ]);
    }

    public function test_teacher_can_create_and_update_own_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)
            ->post(route('teacher.classes.store'), [
                'name' => 'FYJC Information Technology',
                'subject' => 'Information Technology',
                'level' => 'FYJC',
                'academic_year' => '2026-27',
                'description' => 'Accessible IT learning class.',
            ]);

        $class = LearningClass::firstOrFail();

        $response->assertRedirect(route('teacher.classes.show', $class));
        $this->assertSame($teacher->id, $class->teacher_id);
        $this->assertTrue($class->is_active);
        $this->assertStringStartsWith('SG-', $class->code);

        $this->actingAs($teacher)
            ->put(route('teacher.classes.update', $class), [
                'name' => 'FYJC IT - A',
                'subject' => 'Information Technology',
                'level' => 'FYJC A',
                'academic_year' => '2026-27',
                'description' => 'Updated class.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('learning_classes', [
            'id' => $class->id,
            'name' => 'FYJC IT - A',
            'level' => 'FYJC A',
        ]);
    }

    public function test_teacher_cannot_open_another_teachers_class(): void
    {
        $owner = User::factory()->create(['role' => 'teacher']);
        $otherTeacher = User::factory()->create(['role' => 'teacher']);
        $class = LearningClass::create([
            'teacher_id' => $owner->id,
            'name' => 'Private Class',
            'code' => 'SG-OWN001',
            'is_active' => true,
        ]);

        $this->actingAs($otherTeacher)
            ->get(route('teacher.classes.show', $class))
            ->assertForbidden();
    }

    public function test_teacher_can_enroll_active_learner_by_exact_email(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create([
            'role' => 'learner',
            'email' => 'learner@example.com',
            'is_active' => true,
        ]);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Digital Basics',
            'code' => 'SG-CLS001',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.classes.learners.store', $class), [
                'learner_email' => 'learner@example.com',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('class_enrollments', [
            'learning_class_id' => $class->id,
            'learner_id' => $learner->id,
        ]);
    }

    public function test_duplicate_or_non_learner_enrollment_is_rejected(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create([
            'role' => 'learner',
            'email' => 'learner@example.com',
        ]);
        $parent = User::factory()->create([
            'role' => 'parents',
            'email' => 'parent@example.com',
        ]);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Class A',
            'code' => 'SG-CLS002',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $this->actingAs($teacher)
            ->from(route('teacher.classes.show', $class))
            ->post(route('teacher.classes.learners.store', $class), [
                'learner_email' => 'learner@example.com',
            ])
            ->assertSessionHasErrors('learner_email');

        $this->actingAs($teacher)
            ->from(route('teacher.classes.show', $class))
            ->post(route('teacher.classes.learners.store', $class), [
                'learner_email' => $parent->email,
            ])
            ->assertSessionHasErrors('learner_email');
    }

    public function test_archived_class_rejects_new_enrollment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Archived Class',
            'code' => 'SG-CLS003',
            'is_active' => false,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.classes.learners.store', $class), [
                'learner_email' => $learner->email,
            ])
            ->assertSessionHasErrors('learner_email');

        $this->assertDatabaseMissing('class_enrollments', [
            'learning_class_id' => $class->id,
            'learner_id' => $learner->id,
        ]);
    }

    public function test_teacher_can_remove_learner_from_own_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $learner = User::factory()->create(['role' => 'learner']);
        $class = LearningClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Class B',
            'code' => 'SG-CLS004',
            'is_active' => true,
        ]);
        $class->learners()->attach($learner->id, ['enrolled_at' => now()]);

        $this->actingAs($teacher)
            ->delete(route('teacher.classes.learners.destroy', [$class, $learner]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('class_enrollments', [
            'learning_class_id' => $class->id,
            'learner_id' => $learner->id,
        ]);
    }

    public function test_teacher_password_change_requires_current_password(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'password' => 'old-password',
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.profile.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($teacher)
            ->put(route('teacher.profile.password.update'), [
                'current_password' => 'old-password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-password-123', $teacher->fresh()->password));
    }
}
