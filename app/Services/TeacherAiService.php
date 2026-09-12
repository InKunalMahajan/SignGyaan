<?php

namespace App\Services;

use App\Ai\Agents\TeacherAssistant;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class TeacherAiService
{
    public const TASKS = [
        'simplify_english' => 'Simplify English',
        'lesson_plan' => 'Draft lesson plan',
        'quiz_draft' => 'Draft quiz questions',
        'explain_concept' => 'Explain a concept',
        'accessibility_adaptation' => 'Make content more accessible',
    ];

    public function generate(User $teacher, string $task, string $prompt, ?string $context = null): string
    {
        if ($teacher->role !== 'teacher') {
            throw new RuntimeException('Teacher AI is available only to teacher accounts.');
        }

        if (! array_key_exists($task, self::TASKS)) {
            throw new RuntimeException('Unsupported AI task.');
        }

        $provider = (string) config('signgyaan-ai.teacher.provider', 'openai');
        $model = (string) config('signgyaan-ai.teacher.model', 'gpt-5.6-luna');
        $timeout = (int) config('signgyaan-ai.teacher.timeout', 45);

        $request = $this->buildPrompt($task, $prompt, $context);

        try {
            $response = (new TeacherAssistant)->prompt(
                $request,
                provider: $provider,
                model: $model,
                timeout: $timeout,
            );

            $text = trim((string) $response);

            if ($text === '') {
                throw new RuntimeException('The AI provider returned an empty response.');
            }

            Log::info('Teacher AI draft generated', [
                'teacher_id' => $teacher->id,
                'task' => $task,
                'provider' => $provider,
                'model' => $model,
                'prompt_characters' => mb_strlen($prompt),
                'context_characters' => mb_strlen((string) $context),
                'response_characters' => mb_strlen($text),
            ]);

            return $text;
        } catch (Throwable $exception) {
            Log::warning('Teacher AI generation failed', [
                'teacher_id' => $teacher->id,
                'task' => $task,
                'provider' => $provider,
                'model' => $model,
                'exception' => $exception::class,
            ]);

            throw new RuntimeException(
                'AI draft could not be generated right now. Check the OpenAI configuration and try again.',
                previous: $exception,
            );
        }
    }

    private function buildPrompt(string $task, string $prompt, ?string $context): string
    {
        $parts = [
            'Task: '.self::TASKS[$task],
            '',
            'Teacher request:',
            trim($prompt),
        ];

        if (filled($context)) {
            $parts[] = '';
            $parts[] = 'Optional teaching context:';
            $parts[] = trim((string) $context);
        }

        $parts[] = '';
        $parts[] = 'Return a reviewable teaching draft. Do not claim that it has been published or saved.';

        return implode("\n", $parts);
    }
}
