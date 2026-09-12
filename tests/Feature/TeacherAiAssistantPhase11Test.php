<?php

namespace Tests\Feature;

use App\Ai\Agents\TeacherAssistant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Prompts\AgentPrompt;
use Tests\TestCase;

class TeacherAiAssistantPhase11Test extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_open_ai_assistant_and_generate_reviewable_draft(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
        ]);

        TeacherAssistant::fake([
            "Draft lesson plan\n1. Show a visual example.\n2. Ask learners to identify the primary key.",
        ])->preventStrayPrompts();

        $this->actingAs($teacher)
            ->get(route('teacher.ai.index'))
            ->assertOk()
            ->assertSee('AI Assistant')
            ->assertSee('Teacher stays in control')
            ->assertSee('Nothing is automatically published');

        $this->actingAs($teacher)
            ->post(route('teacher.ai.generate'), [
                'task' => 'lesson_plan',
                'prompt' => 'Create a visual lesson plan about database primary keys.',
                'context' => 'FYJC, 30 minutes, simple English.',
            ])
            ->assertOk()
            ->assertSee('AI-generated draft')
            ->assertSee('Draft lesson plan')
            ->assertSee('Not published');

        TeacherAssistant::assertPrompted(function (AgentPrompt $prompt): bool {
            return $prompt->contains('Create a visual lesson plan')
                && $prompt->contains('FYJC, 30 minutes');
        });
    }

    public function test_learner_cannot_open_teacher_ai_assistant(): void
    {
        $learner = User::factory()->create([
            'role' => 'learner',
            'is_active' => true,
        ]);

        $this->actingAs($learner)
            ->get(route('teacher.ai.index'))
            ->assertForbidden();
    }

    public function test_teacher_ai_validates_task_and_prompt_limits_before_provider_call(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'is_active' => true,
        ]);

        TeacherAssistant::fake()->preventStrayPrompts();

        $this->actingAs($teacher)
            ->post(route('teacher.ai.generate'), [
                'task' => 'final_grading',
                'prompt' => 'Give final grades to all learners.',
            ])
            ->assertSessionHasErrors('task');

        TeacherAssistant::assertNothingPrompted();
    }
}
