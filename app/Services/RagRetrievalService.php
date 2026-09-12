<?php

namespace App\Services;

use App\Models\KnowledgeChunk;
use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Ai\Embeddings;

class RagRetrievalService
{
    /**
     * @return Collection<int, array{chunk: KnowledgeChunk, score: float}>
     */
    public function retrieve(User $teacher, string $query, ?int $courseId = null): Collection
    {
        $provider = (string) config('signgyaan-ai.rag.embedding_provider', 'openai');
        $model = (string) config('signgyaan-ai.rag.embedding_model', 'text-embedding-3-small');
        $dimensions = (int) config('signgyaan-ai.rag.embedding_dimensions', 768);
        $timeout = (int) config('signgyaan-ai.rag.timeout', 45);
        $topK = max(1, (int) config('signgyaan-ai.rag.top_k', 5));
        $minimum = (float) config('signgyaan-ai.rag.minimum_similarity', 0.20);

        $queryVector = Embeddings::for([trim($query)])
            ->dimensions($dimensions)
            ->timeout($timeout)
            ->generate(provider: $provider, model: $model)
            ->embeddings[0] ?? [];

        if ($queryVector === []) {
            return collect();
        }

        $chunks = KnowledgeChunk::query()
            ->with(['source.course'])
            ->whereHas('source', function ($builder) use ($teacher, $courseId): void {
                $builder->where('owner_id', $teacher->id)
                    ->where('status', 'indexed');

                if ($courseId !== null) {
                    $builder->where('course_id', $courseId);
                }
            })
            ->get();

        return $chunks
            ->map(fn (KnowledgeChunk $chunk): array => [
                'chunk' => $chunk,
                'score' => $this->cosine($queryVector, $chunk->embedding ?? []),
            ])
            ->filter(fn (array $item): bool => $item['score'] >= $minimum)
            ->sortByDesc('score')
            ->take($topK)
            ->values();
    }

    /** @param list<float|int> $a @param list<float|int> $b */
    private function cosine(array $a, array $b): float
    {
        if ($a === [] || count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $index => $value) {
            $left = (float) $value;
            $right = (float) $b[$index];
            $dot += $left * $right;
            $normA += $left * $left;
            $normB += $right * $right;
        }

        if ($normA <= 0 || $normB <= 0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
