<?php

namespace App\Services;

use App\Ai\Agents\TeacherRagAssistant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class TeacherRagService
{
    public function __construct(private RagRetrievalService $retrieval)
    {
    }

    /**
     * @return array{answer: string, sources: Collection<int, array{label:string,title:string,excerpt:string,score:float}>}
     */
    public function answer(User $teacher, string $question, ?int $courseId = null): array
    {
        if ($teacher->role !== 'teacher') {
            throw new RuntimeException('Knowledge Assistant is available only to teacher accounts.');
        }

        $matches = $this->retrieval->retrieve($teacher, $question, $courseId);

        if ($matches->isEmpty()) {
            return [
                'answer' => 'I could not find enough information in your indexed SignGyaan knowledge sources to answer this question.',
                'sources' => collect(),
            ];
        }

        $sources = $matches->values()->map(function (array $item, int $index): array {
            $chunk = $item['chunk'];

            return [
                'label' => 'Source '.($index + 1),
                'title' => $chunk->source?->title ?? 'Knowledge source',
                'excerpt' => $chunk->content,
                'score' => (float) $item['score'],
            ];
        });

        $context = $sources->map(function (array $source): string {
            return sprintf(
                "[%s] %s\n%s",
                $source['label'],
                $source['title'],
                $source['excerpt'],
            );
        })->implode("\n\n");

        $prompt = "Teacher question:\n".trim($question)
            ."\n\nRetrieved SignGyaan sources:\n".$context
            ."\n\nAnswer only from these sources. Cite supporting statements using [Source 1], [Source 2], etc.";

        $provider = (string) config('signgyaan-ai.teacher.provider', 'openai');
        $model = (string) config('signgyaan-ai.teacher.model', 'gpt-5.6-luna');
        $timeout = (int) config('signgyaan-ai.teacher.timeout', 45);

        try {
            $response = (new TeacherRagAssistant)->prompt(
                $prompt,
                provider: $provider,
                model: $model,
                timeout: $timeout,
            );

            $answer = trim((string) $response);

            if ($answer === '') {
                throw new RuntimeException('The AI provider returned an empty grounded response.');
            }

            Log::info('Teacher RAG answer generated', [
                'teacher_id' => $teacher->id,
                'course_id' => $courseId,
                'source_count' => $sources->count(),
                'question_characters' => mb_strlen($question),
                'answer_characters' => mb_strlen($answer),
            ]);

            return [
                'answer' => $answer,
                'sources' => $sources,
            ];
        } catch (Throwable $exception) {
            Log::warning('Teacher RAG generation failed', [
                'teacher_id' => $teacher->id,
                'course_id' => $courseId,
                'source_count' => $sources->count(),
                'exception' => $exception::class,
            ]);

            throw new RuntimeException(
                'The grounded AI answer could not be generated right now. Check the OpenAI configuration and try again.',
                previous: $exception,
            );
        }
    }
}
