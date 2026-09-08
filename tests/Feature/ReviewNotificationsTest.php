<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\LearningClass;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishing_lesson_notifies_active_admins_only(): void
    {
        [$teacher, $course, $unit] = $this->path(false);
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);
        $inactiveAdmin = User::factory()->create(['role'=>'admin','is_active'=>false]);

        $this->actingAs($teacher)->post(route('teacher.courses.units.lessons.store',[$course,$unit]),[
            'title'=>'Notification Lesson','summary'=>'Summary','notes'=>'Notes','estimated_minutes'=>10,'status'=>'published',
        ])->assertRedirect();

        $this->assertSame(1,$admin->fresh()->notifications()->count());
        $this->assertSame(0,$inactiveAdmin->fresh()->notifications()->count());
        $this->assertSame('review_submitted',$admin->fresh()->notifications()->first()->data['event']);
    }

    public function test_admin_decisions_notify_owning_teacher(): void
    {
        [$teacher,$course,$unit,$lesson] = $this->path();
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);

        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review',$lesson),[
            'review_status'=>'changes_requested','review_notes'=>'Please improve the ISL explanation.',
        ])->assertRedirect();

        $notification=$teacher->fresh()->notifications()->first();
        $this->assertNotNull($notification);
        $this->assertSame('changes_requested',$notification->data['event']);
    }

    public function test_resubmission_notifies_active_admins(): void
    {
        [$teacher,$course,$unit,$lesson] = $this->path();
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);
        $lesson->update(['review_status'=>'changes_requested','review_notes'=>'Fix notes']);

        $this->actingAs($teacher)->patch(route('teacher.reviews.resubmit',$lesson),[
            'teacher_response'=>'Updated the notes.',
        ])->assertRedirect();

        $this->assertSame('review_resubmitted',$admin->fresh()->notifications()->first()->data['event']);
    }

    public function test_teacher_and_admin_can_open_notification_centre_and_mark_read(): void
    {
        [$teacher,$course,$unit,$lesson] = $this->path();
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);
        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review',$lesson),[
            'review_status'=>'approved','review_notes'=>'Ready.',
        ]);

        $notification=$teacher->fresh()->unreadNotifications()->first();
        $this->actingAs($teacher)->get(route('notifications.index'))->assertOk()->assertSee('Lesson approved');
        $this->actingAs($teacher)->patch(route('notifications.read',$notification))->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($admin)->get(route('notifications.index'))->assertOk();
    }

    public function test_user_cannot_mark_another_users_notification_read(): void
    {
        [$teacher,$course,$unit,$lesson] = $this->path();
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);
        $otherTeacher = User::factory()->create(['role'=>'teacher','is_active'=>true]);
        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review',$lesson),['review_status'=>'approved','review_notes'=>'Ready.']);
        $notification=$teacher->fresh()->unreadNotifications()->first();

        $this->actingAs($otherTeacher)->patch(route('notifications.read',$notification))->assertForbidden();
        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_mark_all_read_only_affects_signed_in_user(): void
    {
        [$teacher,$course,$unit,$lesson] = $this->path();
        $admin = User::factory()->create(['role'=>'admin','is_active'=>true]);
        $this->actingAs($admin)->patch(route('admin.curriculum.lessons.review',$lesson),['review_status'=>'approved','review_notes'=>'Ready.']);

        $teacher->notify(new \App\Notifications\ReviewWorkflowNotification('test','Second alert','Second message'));
        $this->assertSame(2,$teacher->fresh()->unreadNotifications()->count());
        $this->actingAs($teacher)->patch(route('notifications.read-all'))->assertRedirect();
        $this->assertSame(0,$teacher->fresh()->unreadNotifications()->count());
    }

    public function test_learner_cannot_open_staff_notification_centre(): void
    {
        $learner=User::factory()->create(['role'=>'learner','is_active'=>true]);
        $this->actingAs($learner)->get(route('notifications.index'))->assertForbidden();
    }

    private function path(bool $withLesson=true): array
    {
        $teacher=User::factory()->create(['role'=>'teacher','is_active'=>true]);
        $class=LearningClass::create(['teacher_id'=>$teacher->id,'name'=>'Digital Skills','code'=>'SG-NOT001','subject'=>'IT','level'=>'Beginner','academic_year'=>'2026-27','is_active'=>true]);
        $subject=Subject::create(['created_by'=>$teacher->id,'name'=>'IT','slug'=>'it-notifications','is_active'=>true]);
        $course=Course::create(['subject_id'=>$subject->id,'created_by'=>$teacher->id,'title'=>'Digital Basics','slug'=>'digital-basics-notifications','is_active'=>true]);
        $class->courses()->attach($course->id,['assigned_by'=>$teacher->id,'assigned_at'=>now()]);
        $unit=CourseUnit::create(['course_id'=>$course->id,'title'=>'Core Unit','position'=>1,'is_active'=>true]);
        $lesson=null;
        if($withLesson){$lesson=Lesson::create(['course_unit_id'=>$unit->id,'title'=>'Intro Lesson','summary'=>'Summary','notes'=>'Notes','estimated_minutes'=>10,'position'=>1,'status'=>'published','review_status'=>'pending','review_submitted_at'=>now(),'published_at'=>now()]);}
        return [$teacher,$course,$unit,$lesson];
    }
}
