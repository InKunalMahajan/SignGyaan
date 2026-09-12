<?php

namespace App\Services;

use App\Models\KnowledgeSource;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Ai\Embeddings;
use RuntimeException;
use Throwable;

class RagIndexService
{
    public function createManualSource(User $teacher, string $title, string $content, ?int $courseId = null): KnowledgeSource
    {
        if ($teacher->role !== 'teacher') {
            throw new RuntimeException('Knowledge sources can only be created by teacher accounts.');
        }

        $source = KnowledgeSource::create([
            'owner_id' => $teacher->id,
            'course_id' => $courseId,
            'title' => trim($title),
            'source_type' => 'manual',
            'source_ref' => null,
            'content' => trim($content),
            'content_hash' => hash('sha256', trim($content)),
            'status' => 'draft',
        ]);

        return $this->index($source);
    }

    public function syncLesson(User $teacher, Lesson $lesson): KnowledgeSource
    {
        $lesson->loadMissing('unit.course');
        $course = $lesson->unit?->course;

        if (! $course || (int) $course->created_by !== (int) $teacher->id) {
            throw new RuntimeException('You can only index lessons from courses you manage.');
        }

        if (! $lesson->isPublished()) {
            throw new RuntimeException('Only published lessons can be added to the knowledge base.');
        }

        $content = collect([
            $lesson->title,
            $lesson->summary,
            $lesson->notes,
        ])->filter(fn ($value) => filled($value))->implode("\n\n");

        if (blank($content)) {
            throw new RuntimeException('This lesson does not contain text that can be indexed.');
        }

        $source = KnowledgeSource::updateOrCreate(
            [
                'owner_id' => $teacher->id,
                'source_type' => 'lesson',
                'source_ref' => (string) $lesson->id,
            ],
            [
                'course_id' => $course->id,
                'title' => $course->title.' — '.$lesson->title,
                'content' => $content,
                'content_hash' => hash('sha256', $content),
                'status' => 'draft',
                'metadata' => [
                    'lesson_id' => $lesson->id,
                    'unit_id' => $lesson->course_unit_id,
                ],
                'last_error' => null,
            ],
        );

        return $this->index($source);
    }

    public function index(KnowledgeSource $source): KnowledgeSource
    {
        $chunks = $this->chunk($source->content);

        if ($chunks === []) {
            throw new RuntimeException('Knowledge source has no text to index.');
        }

        try {
            $provider = (string) config('signgyaan-ai.rag.embedding_provider', 'openai');
            $model = (string) config('signgyaan-ai.rag.embedding_model', 'text-embedding-3-small');
            $dimensions = (int) config('signgyaan-ai.rag.embedding_dimensions', 768);
            $timeout = (int) config('signgyaan-ai.rag.timeout', 45);

            $response = Embeddings::for($chunks)
                ->dimensions($dimensions)
                ->timeout($timeout)
                ->generate(provider: $provider, model: $model);

            $embeddings = $response->embeddings;

            if (count($embeddings) !== count($chunks)) {
                throw new RuntimeException('Embedding response count does not match the knowledge chunks.');
            }

            DB::transaction(function () use ($source, $chunks, $embeddings): void {
                $source->chunks()->delete();

                foreach ($chunks as $index => $chunk) {
                    $source->chunks()->create([
                        'chunk_index' => $index,
                        'content' => $chunk,
                        'embedding' => $embeddings[$index],
                        'character_count' => mb_strlen($chunk),
                        'metadata' => [
                            'source_title' => $source->title,
                        ],
                    ]);
                }

                $source->update([
                    'status' => 'indexed',
                    'indexed_at' => now(),
                    'last_error' => null,
                    'content_hash' => hash('sha256', $source->content),
                ]);
            });
        } catch (Throwable $exception) {
            $source->update([
                'status' => 'error',
                'last_error' => Str::limit($exception->getMessage(), 1000),
            ]);

            throw new RuntimeException('The knowledge source could not be indexed right now.', previous: $exception);
        }

        return $source->fresh(['chunks', 'course']);
    }

    /** @return list<string> */
    public function chunk(string $content): array
    {
        $content = trim(preg_replace('/\s+/u', ' ', $content) ?? '');

        if ($content === '') {
            return [];
        }

        $size = max(400, (int) config('signgyaan-ai.rag.chunk_characters', 1200));
        $overlap = max(0, min($size - 100, (int) config('signgyaan-ai.rag.chunk_overlap', 180)));
        $chunks = [];
        $offset = 0;
        $length = mb_strlen($content);

        while ($offset < $length) {
            $take = min($size, $length - $offset);
            $chunk = trim(mb_substr($content, $offset, $take));

            if ($chunk !== '') {
                $chunks[] = $chunk;
            }

            if ($offset + $take >= $length) {
                break;
            }

            $offset += max(1, $take - $overlap);
        }

        return $chunks;
    }
}
