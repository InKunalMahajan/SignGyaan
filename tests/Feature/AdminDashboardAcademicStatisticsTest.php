<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAcademicStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_academic_statistics(): void
    {
        $admin = User::factory()->admin()->create();

        Subject::factory()->create(['is_published' => true]);
        Subject::factory()->create(['is_published' => false]);
        Course::factory()->create(['is_published' => true, 'is_featured' => true]);
        Course::factory()->create(['is_published' => false]);
        Unit::factory()->create(['is_published' => true]);
        Unit::factory()->create(['is_published' => false]);
        Lesson::factory()->create(['is_published' => true, 'isl_video_url' => 'https://example.com/isl-video']);
        Lesson::factory()->create(['is_published' => false, 'isl_video_url' => null, 'isl_media_asset_id' => null]);
        Assessment::factory()->create(['is_published' => true]);
        Assessment::factory()->create(['is_published' => false]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Academic Statistics Dashboard')
            ->assertSee('Publishing Health')
            ->assertSee('Academic Coverage')
            ->assertSee('ISL-enabled lessons')
            ->assertSee('Featured courses')
            ->assertSee('Recently Updated Courses');
    }

    public function test_academic_statistics_dashboard_handles_an_empty_catalogue(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('0% published')
            ->assertSee('0% of all lessons')
            ->assertSee('No courses have been created yet.');
    }
}
