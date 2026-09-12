<?php

return [
    'teacher' => [
        'provider' => env('SIGNGYAAN_AI_PROVIDER', 'openai'),
        'model' => env('SIGNGYAAN_AI_MODEL', 'gpt-5.6-luna'),
        'timeout' => (int) env('SIGNGYAAN_AI_TIMEOUT', 45),
        'max_prompt_characters' => 4000,
        'max_context_characters' => 2000,
    ],

    'rag' => [
        'embedding_provider' => env('SIGNGYAAN_RAG_EMBEDDING_PROVIDER', 'openai'),
        'embedding_model' => env('SIGNGYAAN_RAG_EMBEDDING_MODEL', 'text-embedding-3-small'),
        'embedding_dimensions' => (int) env('SIGNGYAAN_RAG_EMBEDDING_DIMENSIONS', 768),
        'chunk_characters' => (int) env('SIGNGYAAN_RAG_CHUNK_CHARACTERS', 1200),
        'chunk_overlap' => (int) env('SIGNGYAAN_RAG_CHUNK_OVERLAP', 180),
        'top_k' => (int) env('SIGNGYAAN_RAG_TOP_K', 5),
        'minimum_similarity' => (float) env('SIGNGYAAN_RAG_MINIMUM_SIMILARITY', 0.20),
        'timeout' => (int) env('SIGNGYAAN_RAG_TIMEOUT', 45),
    ],
];
